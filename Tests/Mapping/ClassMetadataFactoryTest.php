<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Mapping\ClassMetadataFactory;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the EntityBuilder class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class ClassMetadataFactoryTest extends TestCase
{
    use Fixtures\Mock\AssociationMock,
        Fixtures\Mock\EntityManagerMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Mock\FieldMock,
        Fixtures\Mock\PrimaryKeyMock,
        Fixtures\Provider\AssociationProvider,
        Fixtures\Provider\EntityProvider,
        Fixtures\Provider\FieldProvider;

    /**
     * Thats the method "createMetadataFor".
     *
     * Test case:
     *     1. Instantiate a ClassMetadataFactory.
     *     2. Create a ClassMetadata with the ClassMetadataFactory.
     *
     * @dataProvider entityProvider
     */
    public function testCreateMetadataFor(array $mapping)
    {
        $factory = new ClassMetadataFactory();
        $factory->setEntityManager($this->getEntityManager());

        $metadata = $factory->createMetadataFor($mapping['name']);

        $this->assertInstanceOf(
            'Doctrine\ORM\Mapping\ClassMetadata',
            $metadata,
            'The factory should create instances of ClassMetadata.'
        );
        $this->assertEquals($mapping['name'], $metadata->getName(), 'The metadata class name is invalid.');
        $this->assertEmpty($metadata->getFieldNames(), 'The metadata should not have any field already mapped.');
        $this->assertEmpty($metadata->getAssociationNames(), 'The metadata should not have any association already mapped.');
    }

    /**
     * Tests that the method "getMetadataFrom" converts Entity into ClassMetadata.
     *
     * Test case:
     *     1. Create an mocked Entity.
     *     2. Instantiate a ClassMetadataBuilder.
     *     3. Convert the Entity into a ClassMetadata with the ClassMetadataBuilder.
     *
     * @dataProvider entityProvider
     */
    public function testGetMetadataFrom(array $mapping)
    {
        $entity = $this->getEntity($mapping['name']);

        $factory = new ClassMetadataFactory();
        $factory->setEntityManager($this->getEntityManager());

        $metadata = $factory->getMetadataFrom($entity);

        $this->assertInstanceOf(
            'Doctrine\ORM\Mapping\ClassMetadata',
            $metadata,
            'The factory should create instances of ClassMetadata.'
        );
        $this->assertEquals($mapping['name'], $metadata->getName(), 'The metadata class name is invalid.');
        $this->assertEmpty($metadata->getFieldNames(), 'The metadata should not have any field already mapped.');
        $this->assertEmpty($metadata->getAssociationNames(), 'The metadata should not have any association already mapped.');
    }

    /**
     * Tests that the method "getMetadataFrom" converts fields.
     *
     * Test case:
     *     1. Create a mocked Entity with a mapped field.
     *     2. Instantiate a ClassMetadataBuilder.
     *     3. Convert the Entity into a ClassMetadata with the ClassMetadataBuilder.
     *
     * @dataProvider fieldProvider
     */
    public function testGetMetadataFromWithField(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post', [
            $mapping['fieldName'] => $this->getField($mapping),
        ]);

        $factory = new ClassMetadataFactory();
        $factory->setEntityManager($this->getEntityManager());

        $metadata = $factory->getMetadataFrom($entity);

        $this->assertCount(1, $metadata->getFieldNames(), 'The metadata should have one mapped field.');
        $this->assertTrue($metadata->hasField($mapping['fieldName']), 'The metadata should own this field.');
        $this->assertEmpty($metadata->getAssociationNames(), 'The metadata should not have any association already mapped.');

        $fieldMapping = $metadata->getFieldMapping($mapping['fieldName']);

        $this->assertEquals($mapping['fieldName'], $fieldMapping['fieldName'], 'The expected name does not match the resolved name.');
        $this->assertEquals($mapping['type'], $fieldMapping['type'], 'The expected type does not match the resolved type.');
        $this->assertEquals($mapping['nullable'], $fieldMapping['nullable'], 'The expected nullability does not match the resolved nullability.');
        $this->assertEquals($mapping['unique'], $fieldMapping['unique'], 'The expected uniqueness does not match the resolved uniqueness.');
        $this->assertEquals($mapping['length'], $fieldMapping['length'], 'The expected length does not match the resolved length.');
        $this->assertEquals($mapping['precision'], $fieldMapping['precision'], 'The expected precision does not match the resolved precision.');
        $this->assertEquals($mapping['scale'], $fieldMapping['scale'], 'The expected scale does not match the resolved scale.');
    }

    /**
     * Tests that the method "getMetadataFrom" converts primary keys.
     *
     * Test case:
     *     1. Create a mocked Entity with a mapped primary key.
     *     2. Instantiate a ClassMetadataBuilder.
     *     3. Convert the Entity into a ClassMetadata with the ClassMetadataBuilder.
     *
     * @dataProvider fieldProvider
     */
    public function testGetMetadataFromWithPK(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post', [
            $mapping['fieldName'] => $this->getPrimaryKey($mapping['fieldName']),
        ]);

        $factory = new ClassMetadataFactory();
        $factory->setEntityManager($this->getEntityManager());

        $metadata = $factory->getMetadataFrom($entity);

        $this->assertCount(1, $metadata->getFieldNames(), 'The metadata should have one mapped field.');
        $this->assertTrue($metadata->hasField($mapping['fieldName']), 'The metadata should own this field.');
        $this->assertTrue($metadata->isIdentifier($mapping['fieldName']), 'This field should be an identifier.');
        $this->assertEmpty($metadata->getAssociationNames(), 'The metadata should not have any association already mapped.');

        $fieldMapping = $metadata->getFieldMapping($mapping['fieldName']);

        $this->assertEquals($mapping['fieldName'], $fieldMapping['fieldName'], 'The expected name does not match the resolved name.');
    }

    /**
     * Tests that the method "getMetadataFrom" converts associations.
     *
     * Test case:
     *     1. Create a mocked Entity with a mapped association.
     *     2. Instantiate a ClassMetadataBuilder.
     *     3. Convert the Entity in a ClassMetadata with the ClassMetadataBuilder.
     *
     * @dataProvider associationProvider
     */
    public function testGetMetadataFromWithAssociation(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post', [], [
            $mapping['fieldName'] => $this->getAssociation($mapping),
        ]);

        $factory = new ClassMetadataFactory();
        $factory->setEntityManager($this->getEntityManager());

        $metadata = $factory->getMetadataFrom($entity);

        $this->assertCount(1, $metadata->getAssociationNames(), 'The metadata should have one mapped association.');
        $this->assertTrue($metadata->hasAssociation($mapping['fieldName']), 'The metadata should own this association.');
        $this->assertEmpty($metadata->getFieldNames(), 'The metadata should not have any field already mapped.');

        $associationMapping = $metadata->getAssociationMapping($mapping['fieldName']);
        // Handle special reverse cases.
        $doctrineType = $this->getDoctrineType($mapping['type'], $mapping['bidirectional']);

        $this->assertEquals($mapping['fieldName'], $associationMapping['fieldName'], 'The expected association name does not match the resolved association name.');
        $this->assertEquals($doctrineType, $associationMapping['type'], 'The expected association type does not match the resolved association type.');
        $this->assertEquals($mapping['targetEntity'], $associationMapping['targetEntity'], 'The expected target entity does not match the resolved target entity.');
        $this->assertEquals($mapping['mappedBy'], $associationMapping['mappedBy'], 'The expected mappedBy property does not match the resolved mappedBy property.');
        $this->assertEquals($mapping['inversedBy'], $associationMapping['inversedBy'], 'The expected ivnersedBy property does not match the resolved inversedBy property.');
    }
}

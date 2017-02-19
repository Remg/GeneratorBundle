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
use Remg\GeneratorBundle\Mapping\EntityBuilder;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the EntityBuilder class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityBuilderTest extends TestCase
{
    use Fixtures\Mock\AssociationMock,
        Fixtures\Mock\ClassMetadataMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Provider\AssociationProvider,
        Fixtures\Provider\EntityProvider,
        Fixtures\Provider\FieldProvider;

    /**
     * Tests the method "newEntityInstance()".
     *
     * Test case:
     *     1. Instantiate an EntityBuilder.
     *     2. Create an Entity with the EntityBuilder.
     *
     * @dataProvider entityProvider
     */
    public function testNewEntityInstance(array $mapping)
    {
        $builder = new EntityBuilder();

        $entity = $builder->newEntityInstance($mapping['name']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\Entity', $entity,
            'The method newEntityInstance should create Entity instances.');
        $this->assertEquals($mapping['name'], $entity->getName(), 'The entity name is wrong.');

        return $entity;
    }

    /**
     * Tests the method "addPrimaryKey()".
     *
     * Test case:
     *     1. Instantiate an EntityBuilder.
     *     2. Create an Entity instance with the builder.
     *     3. Add a primary key to the entity with the builder.
     *
     * @dataProvider entityProvider
     */
    public function testAddPrimaryKey(array $mapping)
    {
        $builder = new EntityBuilder();

        /* @var \Remg\GeneratorBundle\Model\Entity */
        $entity = $builder->newEntityInstance($mapping['name']);

        $builder->addPrimaryKey($entity, 'id');

        $this->assertCount(1, $entity->getFields(), 'The entity should have one mapped field.');
        $this->assertTrue($entity->hasField('id'), 'The entity should have own a field named "id".');
        $this->assertInstanceOf('Remg\GeneratorBundle\Model\PrimaryKey', $entity->getFields()->first(),
            'The only mapped field should be an instance of PrimaryKey.');
    }

    /**
     * Tests that the method "discoverAssociations" discovers, reverses
     * and maps associations to a given entity.
     *
     * Test case:
     *     1. Register an Entity with a bidirectional association targetting %TARGET%.
     *     2. Instantiate an EntityBuilder.
     *     3. Create a new blank Entity named %TARGET% with the EntityBuilder.
     *     4. Discover the target association for the new Entity with the EntityBuilder.
     *
     * @dataProvider bidirectionalProvider
     */
    public function testDiscoverAssociations(array $owningMapping, array $inverseMapping)
    {
        $owningEntity = $this->getEntity($inverseMapping['targetEntity'], [], [
            $owningMapping['fieldName'] => $this->getAssociation($owningMapping),
        ]);

        $builder = new EntityBuilder();

        $inverseEntity = $builder->newEntityInstance($owningMapping['targetEntity']);
        $builder->discoverAssociations($inverseEntity, [$owningEntity]);

        $association = $inverseEntity->getAssociation($inverseMapping['fieldName']);

        $this->assertCount(1, $inverseEntity->getAssociations(), 'The entity should have one mapped association.');
        $this->assertTrue($inverseEntity->hasAssociation($inverseMapping['fieldName']), 'The entity should have an association with this name.');
        $this->assertEquals($inverseMapping['fieldName'], $association->getName(), 'The association name is not properly reversed.');
        $this->assertEquals($inverseMapping['type'], $association->getType(), 'The association type is not properly reversed.');
        $this->assertEquals($inverseMapping['targetEntity'], $association->getTargetEntity(), 'The taret entity name is not properly reversed.');
        $this->assertEquals($inverseMapping['bidirectional'], $association->isBidirectional(), 'The association bidirectionaliy is not properly reversed.');
        $this->assertEquals($inverseMapping['owningSide'], $association->isOwningSide(), 'The association owning side is not properly reversed.');
        $this->assertEquals($inverseMapping['mappedBy'], $association->getMappedBy(), 'The mappedBy property is not properly reversed.');
        $this->assertEquals($inverseMapping['inversedBy'], $association->getInversedBy(), 'The inversedBy property is not properly reversed.');
    }

    /**
     * Same test case as "testDiscoverAssociations" but in the reverse side.
     *
     * @dataProvider bidirectionalProvider
     */
    public function testDiscoverReverseAssociations(array $owningMapping, array $inverseMapping)
    {
        $this->testDiscoverAssociations($inverseMapping, $owningMapping);
    }

    /**
     * Tests that the method "discoverAssociations" discovers, reverses
     * and maps associations to a given entity.
     *
     * Test case:
     *     1. Register an mocked Entity with an unidirectional association
     *        targetting %TARGET%.
     *     2. Instantiate an EntityBuilder.
     *     3. Create a new mocked Entity named %TARGET%.
     *     4. Discover associations for the new Entity with the EntityBuilder.
     *
     * @dataProvider unidirectionalProvider
     */
    public function testDiscoverAssociationsUnidirectional(array $mapping)
    {
        $owningEntity = $this->getEntity('AppBundle\Entity\Post', [], [
            $mapping['fieldName'] => $this->getAssociation($mapping),
        ]);

        $builder = new EntityBuilder();

        $inverseEntity = $builder->newEntityInstance($mapping['targetEntity']);
        $builder->discoverAssociations($inverseEntity, [$owningEntity]);

        $this->assertEmpty($inverseEntity->getAssociations(), 'The entity should not have any mapped association.');
    }

    /**
     * Tests that the method "getEntityFrom()" converts ClassMetadata into
     * Entity instances.
     *
     * Test case:
     *     1. Mock a ClassMetadata.
     *     2. Instantiate an EntityBuilder.
     *     3. Convert the ClassMetadata into an Entity with the EntityBuilder.
     *
     * @dataProvider entityProvider
     */
    public function testGetEntityFrom(array $mapping)
    {
        // Mock a ClassMetadata with no mapping informations.
        $metadata = $this->getClassMetadata($mapping['name']);

        $builder = new EntityBuilder();

        $entity = $builder->getEntityFrom($metadata);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\Entity', $entity,
            'The method getEntityFrom() should return Entity instances.');
        $this->assertEquals($mapping['name'], $entity->getName(), 'The entity name is wrong.');
        $this->assertEmpty($entity->getFields(), 'The entity should not own any field.');
        $this->assertEmpty($entity->getAssociations(), 'The entity should not own any association.');
    }

    /**
     * Tests that the method "getEntityFrom()" converts field mappings to Field
     * instances.
     *
     * Test case:
     *     1. Create a mocked ClassMetadata with a mapped field.
     *     2. Instantiate an EntityBuilder.
     *     3. Convert the ClassMetadata into an Entity with the EntityBuilder.
     *
     * @dataProvider fieldProvider
     */
    public function testGetEntityFromWithField(array $mapping)
    {
        // Mock a ClassMetadata with one field mapping.
        $metadata = $this->getClassMetadata('entityName', [
            $mapping['fieldName'] => $mapping,
        ]);

        $builder = new EntityBuilder();

        $entity = $builder->getEntityFrom($metadata);

        $this->assertCount(1, $entity->getFields(), 'The entity should own one field.');
        $this->assertTrue($entity->hasField($mapping['fieldName']), 'The entity should own this field.');

        $field = $entity->getField($mapping['fieldName']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\Field', $field, 'The mapped field should be a Field instance.');
        $this->assertEquals($mapping['fieldName'], $field->getName(), 'The field name is not properly mapped.');
        $this->assertEquals($mapping['type'], $field->getType(), 'The field type is not properly mapped.');
        $this->assertEquals($mapping['nullable'], $field->isNullable(), 'The field nullability is not properly mapped.');
        $this->assertEquals($mapping['unique'], $field->isUnique(), 'The field uniqueness is not properly mapped.');
        $this->assertEquals($mapping['length'], $field->getLength(), 'The field length is not properly mapped.');
        $this->assertEquals($mapping['precision'], $field->getPrecision(), 'The field precision is not properly mapped.');
        $this->assertEquals($mapping['scale'], $field->getScale(), 'The field scale is not properly mapped.');
    }

    /**
     * Tests that the method "getEntityFrom()" converts primary key mappings to
     * PrimaryKey instances.
     *
     * Test case:
     *     1. Create a mocked ClassMetadata with an identifier field.
     *     2. Instantiate an EntityBuilder.
     *     3. Convert the ClassMetadata into an Entity with the EntityBuilder.
     *
     * @dataProvider fieldProvider
     */
    public function testGetEntityFromWithPK(array $mapping)
    {
        $metadata = $this->getClassMetadata('entityName', [
            $mapping['fieldName'] => $mapping,
        ]);
        $metadata
            ->expects($this->once())
            ->method('isIdentifier')
            ->with($mapping['fieldName'])
            ->willReturn(true);

        $builder = new EntityBuilder();

        $entity = $builder->getEntityFrom($metadata);

        $this->assertCount(1, $entity->getFields(), 'The entity should own one field.');
        $this->assertTrue($entity->hasField($mapping['fieldName']), 'The entity should own this field.');

        $field = $entity->getField($mapping['fieldName']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\PrimaryKey', $field, 'The mapped field should be a PrimaryKey instance.');
        $this->assertEquals($mapping['fieldName'], $field->getName(), 'The field name is not properly mapped.');
    }

    /**
     * Tests that the method "getEntityFrom()" converts association mappings to
     * Association instances.
     *
     * Test case:
     *     1. Create a mocked ClassMetadata with a mapped association.
     *     2. Instantiate an EntityBuilder.
     *     3. Convert the ClassMetadata in an Entity with the EntityBuilder.
     *
     * @dataProvider associationProvider
     */
    public function testGetEntityFromWithAssociation(array $mapping)
    {
        $metadata = $this->getClassMetadata('entityName', [], [
            $mapping['fieldName'] => $mapping,
        ]);

        $builder = new EntityBuilder();

        $entity = $builder->getEntityFrom($metadata);

        $this->assertCount(1, $entity->getAssociations(), 'The entity should own one association.');
        $this->assertTrue($entity->hasAssociation($mapping['fieldName']), 'The entity should own this association.');

        $association = $entity->getAssociation($mapping['fieldName']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\Association', $association, 'The mapped association should be an Association instance.');
        $this->assertEquals($mapping['fieldName'], $association->getName(), 'The association name is not properly mapped.');
        $this->assertEquals($mapping['type'], $association->getType(), 'The association type is not properly mapped.');
        $this->assertEquals($mapping['targetEntity'], $association->getTargetEntity(), 'The association target entity is not properly mapped.');
        $this->assertEquals($mapping['bidirectional'], $association->isBidirectional(), 'The association bidirectionality is not properly mapped.');
        $this->assertEquals($mapping['owningSide'], $association->isOwningSide(), 'The association owning side is not properly mapped.');
        $this->assertEquals($mapping['mappedBy'], $association->getMappedBy(), 'The association mappedBy property is not properly mapped.');
        $this->assertEquals($mapping['inversedBy'], $association->getInversedBy(), 'The association inversedBy property is not properly mapped.');
    }
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Model\Association;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the Association class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class AssociationTest extends TestCase
{
    use Fixtures\Mock\EntityMock,
        Fixtures\Provider\AssociationProvider;

    /**
     * Tests the basic setters and getters.
     *
     * @dataProvider associationProvider
     */
    public function testGetSet(array $mapping)
    {
        $entity = $this->getEntity();

        $association = new Association();
        $association
            ->setEntity($entity)
            ->setName($mapping['fieldName'])
            ->setType($mapping['type'])
            ->setTargetEntity($mapping['targetEntity'])
            ->setBidirectional($mapping['bidirectional'])
            ->setOwningSide($mapping['owningSide'])
            ->setMappedBy($mapping['mappedBy'])
            ->setInversedBy($mapping['inversedBy']);

        $this->assertEquals($entity, $association->getEntity(), 'The entity should not have changed.');
        $this->assertEquals($mapping['fieldName'], $association->getName(), 'The name should not have changed.');
        $this->assertEquals($mapping['type'], $association->getType(), 'The type should not have changed.');
        $this->assertEquals($mapping['targetEntity'], $association->getTargetEntity(), 'The target entity should not have changed.');
        $this->assertEquals($mapping['bidirectional'], $association->isBidirectional(), 'The bidirectionality should not have changed.');
        $this->assertEquals($mapping['owningSide'], $association->isOwningSide(), 'The owning side should not have changed.');
        $this->assertEquals($mapping['mappedBy'], $association->getMappedBy(), 'The mappedBy property should not have changed.');
        $this->assertEquals($mapping['inversedBy'], $association->getInversedBy(), 'The inversedBy property should not have changed.');
    }

    /**
     * Tests the method "__construct()".
     *
     * @dataProvider associationProvider
     */
    public function testConstructWithMapping(array $mapping)
    {
        $associationMapping = [
            'fieldName'    => $mapping['fieldName'],
            'type'         => $mapping['type'],
            'targetEntity' => $mapping['targetEntity'],
            'mappedBy'     => $mapping['mappedBy'],
            'inversedBy'   => $mapping['inversedBy'],
        ];

        $association = new Association($associationMapping);

        $this->assertEquals($mapping['fieldName'], $association->getName(), 'The name is not well mapped.');
        $this->assertEquals($mapping['type'], $association->getType(), 'The type is not well mapped.');
        $this->assertEquals($mapping['targetEntity'], $association->getTargetEntity(), 'The target is not well mapped.');
        $this->assertEquals($mapping['bidirectional'], $association->isBidirectional(), 'The bidirectionality is not well computed.');
        $this->assertEquals($mapping['owningSide'], $association->isOwningSide(), 'The owning side is not well computed.');
        $this->assertEquals($mapping['mappedBy'], $association->getMappedBy(), 'The mappedBy property is not well mapped.');
        $this->assertEquals($mapping['inversedBy'], $association->getInversedBy(), 'The inversedBy property is not well mapped.');
    }

    /**
     * Tests the method "getDoctrineType()".
     *
     * @see \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function testGetDoctrineType()
    {
        $association = new Association();

        $association->setType('OneToOne');
        $this->assertEquals(1, $association->getDoctrineType(), 'OneToOne association type is "1" for Doctrine.');

        $association->setType('ManyToOne');
        $this->assertEquals(2, $association->getDoctrineType(), 'ManyToOne association type is "2" for Doctrine.');

        $association->setType('OneToMany');
        $this->assertEquals(4, $association->getDoctrineType(), 'OneToMany association type is "4" for Doctrine.');

        $association->setType('ManyToMany');
        $this->assertEquals(8, $association->getDoctrineType(), 'ManyToMany association type is "8" for Doctrine.');

        // @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
        $association
            ->setType('OneToMany')
            ->setBidirectional(false);
        $this->assertEquals(8, $association->getDoctrineType(), 'ManyToMany association type is "8" for this special case.');
    }

    /**
     * Tests the method "__toString()".
     *
     * @dataProvider associationProvider
     */
    public function testToString(array $mapping)
    {
        $association = new Association();
        $association->setName($mapping['fieldName']);

        $this->assertEquals($mapping['fieldName'], $association, 'The __toString() method must return the association name.');
    }

    /**
     * Tests the method "getSupportedTypes()".
     */
    public function testGetSupportedTypes()
    {
        $association = new Association();

        $types = $association->getSupportedTypes();

        $this->assertEquals('OneToOne', $types[0], 'The 1st type must be "OneToOne".');
        $this->assertEquals('ManyToOne', $types[1], 'The 2nd type must be "ManyToOne".');
        $this->assertEquals('OneToMany', $types[2], 'The 3rd type must be "OneToMany".');
        $this->assertEquals('ManyToMany', $types[3], 'The 4th type must be "ManyToMany".');
        $this->assertCount(4, $types, 'There should be 4 supported types.');
    }
}

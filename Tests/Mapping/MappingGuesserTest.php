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
use Remg\GeneratorBundle\Mapping\MappingGuesser;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the MappingGuesser class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class MappingGuesserTest extends TestCase
{
    use Fixtures\Mock\AssociationMock,
        Fixtures\Mock\BundleMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Mock\FieldMock,
        Fixtures\Provider\AssociationProvider,
        Fixtures\Provider\FieldProvider;

    /**
     * Tests the method "guessFieldType()" on a field which already has
     * a defined type.
     *
     * @dataProvider fieldProvider
     */
    public function testGuessFieldTypeDefault(array $mapping)
    {
        $field = $this->getField($mapping);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessFieldType($field);

        $this->assertEquals($mapping['type'], $guess, 'The guessed type should be the defined type.');
    }

    /**
     * Tests the method "guessFieldType()".
     *
     * @dataProvider fieldTypeGuessProvider
     */
    public function testGuessFieldType($name, $type)
    {
        $field = $this->getField(['fieldName' => $name]);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessFieldType($field);

        $this->assertEquals($type, $guess, 'The guessed type is wrong.');
    }

    /**
     * Tests the method "guessAssociationType()" on an association which
     * already has a defined type.
     *
     * @dataProvider associationProvider
     */
    public function testGuessAssociationTypeDefault(array $mapping)
    {
        $association = $this->getAssociation($mapping);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessAssociationType($association);

        $this->assertEquals($mapping['type'], $guess, 'The guessed type should be the defined type.');
    }

    /**
     * Tests the method "guessAssociationType()".
     *
     * @dataProvider associationTypeGuessProvider
     */
    public function testGuessAssociationType($name, $type)
    {
        $association = $this->getAssociation(['fieldName' => $name]);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessAssociationType($association);

        $this->assertEquals($type, $guess, 'The guessed type is wrong.');
    }

    /**
     * Tests the method "testGuessTargetEntity()" on an association which
     * already has a defined target entity.
     *
     * @dataProvider associationProvider
     */
    public function testGuessTargetEntityDefault(array $mapping)
    {
        $association = $this->getAssociation($mapping);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessTargetEntity($association);

        $this->assertEquals($mapping['targetEntity'], $guess, 'The guessed target entity should be the defined target entity.');
    }

    /**
     * Tests the method "guessTargetEntity()".
     *
     * @dataProvider associationTargetEntityGuessProvider
     */
    public function testGuessTargetEntity($name, $bundleName, $targetEntity)
    {
        $bundle = $this->getBundle($bundleName);
        $entity = $this->getEntity();
        $association = $this->getAssociation(['fieldName' => $name]);

        $association->method('getEntity')->willReturn($entity);
        $entity->method('getBundle')->willReturn($bundle);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessTargetEntity($association);

        $this->assertEquals($targetEntity, $guess, 'The guessed target entity is wrong.');
    }

    /**
     * Tests the method "guessInverseProperty()" on an association which
     * already has a defined inversedBy property.
     *
     * @dataProvider bidirectionalProvider
     */
    public function testGuessInversePropertyDefault(array $owning, array $inverse)
    {
        $owningAssociation = $this->getAssociation($owning);
        $inverseAssociation = $this->getAssociation($inverse);

        $guesser = new MappingGuesser();

        $guessOwning = $guesser->guessInverseProperty($owningAssociation);
        $guessInverse = $guesser->guessInverseProperty($inverseAssociation);

        $this->assertEquals($owning['inversedBy'], $guessOwning, 'The guessed inverse property should be the inversedBy property for the owning side.');
        $this->assertEquals($inverse['mappedBy'], $guessInverse, 'The guessed inverse property should be the mappedBy property for the inversed side.');
    }

    /**
     * Tests the method "guessInverseProperty()".
     *
     * @dataProvider associationInversePropertyGuessProvider
     */
    public function testGuessInverseProperty($shortName, $type, $inverseProperty)
    {
        $entity = $this->getEntity();
        $association = $this->getAssociation();

        $association->method('getEntity')->willReturn($entity);
        $association->method('getType')->willReturn($type);
        $entity->method('getShortName')->willReturn($shortName);

        $guesser = new MappingGuesser();

        $guess = $guesser->guessInverseProperty($association);

        $this->assertEquals($inverseProperty, $guess, 'The guessed inverse property is wrong.');
    }

    /**
     * Tests the method "guessIfBidirectional()".
     */
    public function testGuessIfBidirectional()
    {
        $guesser = new MappingGuesser();

        $guess = $guesser->guessIfBidirectional($this->getAssociation());

        $this->assertTrue($guess, 'The guess should be TRUE.');
    }

    /**
     * Tests the method "guessIfOwningSide()".
     */
    public function testGuessIfOwningSide()
    {
        $guesser = new MappingGuesser();

        $guess = $guesser->guessIfOwningSide($this->getAssociation());

        $this->assertTrue($guess, 'The guess should be TRUE.');
    }
}

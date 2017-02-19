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
use Remg\GeneratorBundle\Model\Field;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the Field class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class FieldTest extends TestCase
{
    use Fixtures\Mock\EntityMock,
        Fixtures\Provider\FieldProvider;

    /**
     * Tests the basic setters and getters.
     *
     * @dataProvider fieldProvider
     */
    public function testGetSet(array $mapping)
    {
        $entity = $this->getEntity();

        $field = new Field();
        $field
            ->setEntity($entity)
            ->setName($mapping['fieldName'])
            ->setType($mapping['type'])
            ->setNullable($mapping['nullable'])
            ->setUnique($mapping['unique'])
            ->setLength($mapping['length'])
            ->setPrecision($mapping['precision'])
            ->setScale($mapping['scale']);

        $this->assertEquals($entity, $field->getEntity(), 'The Entity should not have changed.');
        $this->assertEquals($mapping['fieldName'], $field->getName(), 'The name should not have changed.');
        $this->assertEquals($mapping['type'], $field->getType(), 'The type should not have changed.');
        $this->assertEquals($mapping['nullable'], $field->isNullable(), 'The nullability should not have changed.');
        $this->assertEquals($mapping['unique'], $field->isUnique(), 'The uniqueness should not have changed.');
        $this->assertEquals($mapping['length'], $field->getLength(), 'The length should not have changed.');
        $this->assertEquals($mapping['precision'], $field->getPrecision(), 'The precision should not have changed.');
        $this->assertEquals($mapping['scale'], $field->getScale(), 'The scale should not have changed.');
    }

    /**
     * Tests the  method "__construct()".
     *
     * @dataProvider fieldProvider
     */
    public function testConstruct(array $mapping)
    {
        $fieldMapping = [
            'fieldName' => $mapping['fieldName'],
            'type'      => $mapping['type'],
            'nullable'  => $mapping['nullable'],
            'unique'    => $mapping['unique'],
            'length'    => $mapping['length'],
            'precision' => $mapping['precision'],
            'scale'     => $mapping['scale'],
        ];

        $field = new Field($fieldMapping);

        $this->assertEquals($mapping['fieldName'], $field->getName(), 'The name is not properly mapped.');
        $this->assertEquals($mapping['type'], $field->getType(), 'The type is not properly mapped.');
        $this->assertEquals($mapping['nullable'], $field->isNullable(), 'The nullability is not properly mapped.');
        $this->assertEquals($mapping['unique'], $field->isUnique(), 'The uniqueness is not properly mapped.');
        $this->assertEquals($mapping['length'], $field->getLength(), 'The length is not properly mapped.');
        $this->assertEquals($mapping['precision'], $field->getPrecision(), 'The precision is not properly mapped.');
        $this->assertEquals($mapping['scale'], $field->getScale(), 'The scale is not properly mapped.');
    }

    /**
     * Tests the method "__toString()".
     *
     * @dataProvider fieldProvider
     */
    public function testToString(array $mapping)
    {
        $field = new Field();
        $field->setName($mapping['fieldName']);

        $this->assertEquals($mapping['fieldName'], $field, 'The __toString() method must return the field name.');
    }

    /**
     * Tests the method "getSupportedTypes()".
     */
    public function testGetSupportedTypes()
    {
        $field = new Field();

        $types = $field->getSupportedTypes();

        $this->assertEquals('string', $types[0], 'The 1st type must be "string".');
        $this->assertEquals('text', $types[1], 'The 2nd type must be "text".');
        $this->assertEquals('integer', $types[2], 'The 3rd type must be "integer".');
        $this->assertEquals('decimal', $types[3], 'The 4th type must be "decimal".');
        $this->assertEquals('boolean', $types[4], 'The 5th type must be "boolean".');
        $this->assertEquals('datetime', $types[5], 'The 6th type must be "datetime".');
        $this->assertEquals('date', $types[6], 'The 7th type must be "date".');
        $this->assertEquals('time', $types[7], 'The 8th type must be "date".');
    }
}

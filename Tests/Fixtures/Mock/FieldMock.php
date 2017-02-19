<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Fixtures\Mock;

/**
 * Mockery for the Field class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait FieldMock
{
    /**
     * Creates a mocked FieldInterface.
     *
     * @param array $mapping The field mapping informations
     *                       [
     *                       'fieldName' => The field name.
     *                       'type'      => The field type.
     *                       'nullable'  => Whether the field is nullable.
     *                       'unique'    => Whether the field is unique.
     *                       'length'    => The maximum field length.
     *                       'precision' => The field precision.
     *                       'scale'     => The field scale.
     *                       ]
     *
     * @return FieldInterface A mocked FieldInterface.
     */
    protected function getField(array $mapping = [])
    {
        $field = $this
            ->getMockBuilder('Remg\GeneratorBundle\Model\FieldInterface')
            ->getMock();

        if (isset($mapping['fieldName'])) {
            $field
                ->method('getName')
                ->willReturn($mapping['fieldName']);
        }

        if (isset($mapping['type'])) {
            $field
                ->method('getType')
                ->willReturn($mapping['type']);
        }

        if (isset($mapping['nullable'])) {
            $field
                ->method('isNullable')
                ->willReturn($mapping['nullable']);
        }

        if (isset($mapping['unique'])) {
            $field
                ->method('isUnique')
                ->willReturn($mapping['unique']);
        }

        if (isset($mapping['length'])) {
            $field
                ->method('getLength')
                ->willReturn($mapping['length']);
        }

        if (isset($mapping['precision'])) {
            $field
                ->method('getPrecision')
                ->willReturn($mapping['precision']);
        }

        if (isset($mapping['scale'])) {
            $field
                ->method('getScale')
                ->willReturn($mapping['scale']);
        }

        return $field;
    }
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Fixtures\Provider;

use Symfony\Component\Yaml\Yaml;

/**
 * Provider related to field mappings.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait FieldProvider
{
    /**
     * Provides test cases for entity fields.
     *
     * @return array[] An array of field mapping informations.
     *                 [
     *                 0 => The field name.
     *                 1 => The field type.
     *                 2 => Whether the field is nullable.
     *                 3 => Whether the field is unique.
     *                 4 => The field length.
     *                 5 => The field precision.
     *                 6 => The field scale.
     *                 ]
     */
    public function fieldProvider()
    {
        $fields = Yaml::parse(file_get_contents(__DIR__.'/Data/Field.yml'));

        $mappings = [];
        foreach ($fields as $mapping) {
            $mappings[] = [$mapping];
        }

        return $mappings;
    }

    /**
     * Provides test cases for guessing a field type.
     *
     * @return array[] An array of a field type guess test case.
     *                 [
     *                 0 => The field name.
     *                 1 => The field type.
     *                 ]
     */
    public function fieldTypeGuessProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/FieldTypeGuess.yml'));
    }

    /**
     * Provides test cases detecting invalid field name regarding php rules.
     *
     * @return array[] An array of invalid field names.
     *                 [
     *                 0 => The invalid field name.
     *                 ]
     */
    public function fieldInvalidPhpProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/FieldInputInvalidPhp.yml'));
    }

    /**
     * Provides test cases for validating field lengths.
     *
     * @return array[] An array of valid field lengths.
     *                 [
     *                 0 => A valid field length.
     *                 ]
     */
    public function fieldLengthProvider()
    {
        return [
            [10],
            [64],
            [128],
            [255],
        ];
    }

    /**
     * Provides test cases for validating field precisions.
     *
     * @return array[] An array of valid field precisions.
     *                 [
     *                 0 => A valid field precision.
     *                 ]
     */
    public function fieldPrecisionProvider()
    {
        return [
            [2],
            [10],
            [35],
            [65],
        ];
    }

    /**
     * Provides test cases for detecting too high field precisions.
     *
     * @return array[] An array of too high field precisions.
     *                 [
     *                 0 => A too high field precision.
     *                 ]
     */
    public function precisionGreaterThanMaximumProvider()
    {
        return [
            [66],
            [70],
            [100],
            [150],
        ];
    }

    /**
     * Provides test cases for validating field scales.
     *
     * @return array[] An array of valid field scales.
     *                 [
     *                 0 => A valid field scale.
     *                 1 => The corresponding field precision.
     *                 ]
     */
    public function fieldScaleProvider()
    {
        return [
            [2, 10],
            [2, 12],
            [10, 20],
            [30, 65],
        ];
    }

    /**
     * Provides test cases for detecting too high field scales.
     *
     * @return array[] An array of too high field scales.
     *                 [
     *                 0 => A too high field scale.
     *                 ]
     */
    public function scaleGreaterThanMaximumProvider()
    {
        return [
            [31],
            [40],
            [75],
            [100],
        ];
    }

    /**
     * Provides test cases for detecting scales greater than precisions.
     *
     * @return array[] An array of scales greater than precisions.
     *                 [
     *                 0 => The field scale.
     *                 1 => The field precision.
     *                 ]
     */
    public function scaleGreaterThanPrecisionProvider()
    {
        return [
            [2, 1],
            [10, 2],
            [20, 10],
            [10, 10],
        ];
    }

    /**
     * Provides test cases for detecting invalid integers.
     *
     * @return array[] An array of invalid integers.
     *                 [
     *                 0 => An invalid integer.
     *                 ]
     */
    public function invalidIntegerProvider()
    {
        return [
            [false],
            [null],
            ['abc'],
            [1.1],
            ['1.1'],
            ['1,1'],
            ['0.0'],
        ];
    }

    /**
     * Provides test cases for detecting negative integers.
     *
     * @return array[] An array of negative integers.
     *                 [
     *                 0 => A negative integer.
     *                 ]
     */
    public function negativeIntegerProvider()
    {
        return [
            [-1],
            [-10],
            [-64],
            [-128],
            [-255],
        ];
    }
}

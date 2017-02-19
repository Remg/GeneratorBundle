<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Mapping;

use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * Contract for a mapping validator class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface MappingValidatorInterface
{
    /**
     * Validates an entity name to be created.
     *
     * This method asserts that:
     *   1. The given name must be a valid entity name (fqcn or shortcut notation).
     *   2. The given entity name must not already exist.
     *   3. If a shortcut notation is given, the bundle name must be known.
     *
     * @param string $name The new entity name to validate.
     *
     * @return string The new entity fully qualified class name.
     */
    public function validateNewName($name);

    /**
     * Validates an entity field/association name.
     *
     * This method asserts that:
     *   1. The field name is not null or empty.
     *   2. The field name is not already mapped.
     *   3. The field name must be a valid PHP variable name.
     *   4. The field name is not a reserved keyword.
     *
     * @param EntityInterface $entity The entity owning the field.
     * @param string          $name   The field (or association) name to validate.
     *
     * @throws MappingException If one of the assertion fails.
     */
    public function validateFieldName(EntityInterface $entity, $name);

    /**
     * Validates a field length.
     *
     * This method asserts that:
     *   1. The given length is a valid integer.
     *   2. The given length is positive.
     *
     * @param int $length The length being validated.
     *
     * @throws FieldMappingException If one of the assertion fails.
     */
    public function validateFieldLength($length);

    /**
     * Validates that a given field precision is valid.
     *
     * This method asserts that:
     *   1. The given precision is a valid integer.
     *   2. The given precision is positive.
     *   3. The given precision is not greater than self::MAXIMUM_PRECISION.
     *
     * @param int $precision The precision being validated.
     *
     * @throws FieldMappingException If one of the assertion fails.
     */
    public function validateFieldPrecision($precision);

    /**
     * Validates that a given field scale is valid.
     *
     * This method asserts that:
     *   1. The given scale is a valid integer.
     *   2. The given scale is positive.
     *   3. The given scale is not greater than self::MAXIMUM_SCALE.
     *   3. The given scale is not greater than the given precision.
     *
     * @param int $scale     The scale being validated.
     * @param int $precision The precision of the current field.
     *
     * @throws FieldMappingException If one of the assertion fails.
     */
    public function validateFieldScale($scale, $precision);

    /**
     * Validates an association target entity.
     *
     * This method asserts that:
     *   1. The given name must be a valid entity name (fqcn or shortcut notation).
     *   2. If a shortcut notation is given, the bundle name must be known.
     *
     * @param string $name The field (or association) name to validate
     *
     * @throws MappingException        If one of the naming assertion fails.
     * @throws BundleNotFoundException If the bundle can not be retrieved.
     */
    public function validateTargetEntity($name);
}

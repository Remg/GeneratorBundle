<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Model;

/**
 * Contract for an entity field model class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface FieldInterface
{
    /**
     * Instantiate a FieldInterface that can aggregate with a given mapping.
     *
     * @param array $mapping The field mapping informations.
     *                       [
     *                       'fieldName' => The field name.
     *                       'type'      => The field type.
     *                       'nullable'  => Whether the field is nullable.
     *                       'unique'    => Whether the field is unique.
     *                       'length'    => The maximum field length.
     *                       'precision' => The field precision.
     *                       'scale'     => The field scale.
     *                       ]
     */
    public function __construct(array $mapping = null);

    /**
     * Gets the string representation of the field.
     *
     * @return string
     */
    public function __toString();

    /**
     * Sets the field name.
     *
     * @param string $name
     *
     * @return FieldInterface
     */
    public function setName($name);

    /**
     * Gets the field name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the field type.
     *
     * @param string $type
     *
     * @return FieldInterface
     */
    public function setType($type);

    /**
     * Gets the field type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns an indexed array of all supported types.
     *
     * @return array
     */
    public function getSupportedTypes();

    /**
     * Sets whether the field is nullable.
     *
     * @param bool $nullable
     *
     * @return FieldInterface
     */
    public function setNullable($nullable);

    /**
     * Gets whether the field is nullable.
     *
     * @return bool
     */
    public function isNullable();

    /**
     * Sets whether the field is unique.
     *
     * @param bool $unique
     *
     * @return FieldInterface
     */
    public function setUnique($unique);

    /**
     * Gets whether the field is unique.
     *
     * @return bool
     */
    public function isUnique();

    /**
     * Sets the field length.
     *
     * @param int $length
     *
     * @return FieldInterface
     */
    public function setLength($length);

    /**
     * Gets the field length.
     *
     * @return int
     */
    public function getLength();

    /**
     * Sets the field precision.
     *
     * @param int $precision
     *
     * @return FieldInterface
     */
    public function setPrecision($precision);

    /**
     * Gets the field precision.
     *
     * @return int
     */
    public function getPrecision();

    /**
     * Sets the field scale.
     *
     * @param int $scale
     *
     * @return FieldInterface
     */
    public function setScale($scale);

    /**
     * Gets the field scale.
     *
     * @return int
     */
    public function getScale();

    /**
     * Sets the owning entity.
     *
     * @param EntityInterface $entity
     *
     * @return FieldInterface
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Gets the owning entity.
     *
     * @return EntityInterface
     */
    public function getEntity();
}

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

use Doctrine\DBAL\Types\Type;

/**
 * Represents a field of an entity.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class Field implements FieldInterface
{
    /**
     * Contains the field name.
     *
     * @var string
     */
    private $name;

    /**
     * Contains the field type.
     *
     * @var string
     */
    private $type;

    /**
     * Contains whether the field is nullable.
     *
     * @var bool
     */
    private $nullable;

    /**
     * Contains whether the field is unique.
     *
     * @var bool
     */
    private $unique;

    /**
     * Contains the field length.
     *
     * Only applies for string fields.
     *
     * @var int
     */
    private $length;

    /**
     * Contains the field precision.
     *
     * Only applies for decimal fields.
     *
     * @var int
     */
    private $precision;

    /**
     * Contains the field scale.
     *
     * Only applies for decimal fields.
     *
     * @var int
     */
    private $scale;

    /**
     * Contains the owning entity.
     *
     * @var Entity
     */
    private $entity;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $mapping = null)
    {
        if ($mapping) {
            $mapping = array_merge([
                'fieldName' => null,
                'type'      => null,
                'nullable'  => null,
                'unique'    => null,
                'length'    => null,
                'precision' => null,
                'scale'     => null,
            ], $mapping);
            $this->name = $mapping['fieldName'];
            $this->type = $mapping['type'];
            $this->nullable = $mapping['nullable'];
            $this->unique = $mapping['unique'];
            $this->length = $mapping['length'];
            $this->precision = $mapping['precision'];
            $this->scale = $mapping['scale'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes()
    {
        return array_values(array_unique(array_merge(
            [
                Type::STRING,
                Type::TEXT,
                Type::INTEGER,
                Type::DECIMAL,
                Type::BOOLEAN,
                Type::DATETIME,
                Type::DATE,
                Type::TIME,
            ],
            array_keys(call_user_func([Type::class, 'getTypesMap']))
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * {@inheritdoc}
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * {@inheritdoc}
     */
    public function setScale($scale)
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return $this->entity;
    }
}

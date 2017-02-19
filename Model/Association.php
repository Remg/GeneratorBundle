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

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Represents an association of an entity.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class Association implements AssociationInterface
{
    const ONE_TO_ONE = 'OneToOne';
    const ONE_TO_MANY = 'OneToMany';
    const MANY_TO_ONE = 'ManyToOne';
    const MANY_TO_MANY = 'ManyToMany';

    /**
     * Contains the association name.
     *
     * @var string
     */
    private $name;

    /**
     * Contains the association type alias.
     *
     * @var string
     */
    private $type;

    /**
     * Contains the association target entity fully qualified class name.
     *
     * @var string
     */
    private $targetEntity;

    /**
     * Contains whether the association is bidirectional.
     *
     * @var bool
     */
    private $bidirectional;

    /**
     * Contains whether the association is on the owning side of the relation.
     *
     * @var bool
     */
    private $owningSide;

    /**
     * Contains the association mappedBy property.
     *
     * @var string
     */
    private $mappedBy;

    /**
     * Contains the association inversedBy property.
     *
     * @var string
     */
    private $inversedBy;

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
            $this->name = $mapping['fieldName'];

            switch ($mapping['type']) {
                case ClassMetadata::ONE_TO_ONE:
                    $this->type = static::ONE_TO_ONE;
                    break;
                case ClassMetadata::MANY_TO_ONE:
                    $this->type = static::MANY_TO_ONE;
                    break;
                case ClassMetadata::ONE_TO_MANY:
                    $this->type = static::ONE_TO_MANY;
                    break;
                case ClassMetadata::MANY_TO_MANY:
                    $this->type = static::MANY_TO_MANY;
                    break;
                default:
                    $this->type = $mapping['type'];
                    break;
            }

            $this->targetEntity = $mapping['targetEntity'];
            $this->mappedBy = $mapping['mappedBy'];
            $this->inversedBy = $mapping['inversedBy'];
            $this->bidirectional = $this->mappedBy || $this->inversedBy;
            $this->owningSide = $this->bidirectional ? (bool) $this->inversedBy : null;
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
        return [
            static::ONE_TO_ONE,
            static::MANY_TO_ONE,
            static::ONE_TO_MANY,
            static::MANY_TO_MANY,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDoctrineType()
    {
        if (
            static::ONE_TO_MANY === $this->type
            && false === $this->bidirectional
        ) {
            return ClassMetadata::MANY_TO_MANY;
        }

        switch ($this->type) {
            case static::ONE_TO_ONE:
                $type = ClassMetadata::ONE_TO_ONE;
                break;
            case static::MANY_TO_ONE:
                $type = ClassMetadata::MANY_TO_ONE;
                break;
            case static::ONE_TO_MANY:
                $type = ClassMetadata::ONE_TO_MANY;
                break;
            case static::MANY_TO_MANY:
                $type = ClassMetadata::MANY_TO_MANY;
                break;
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetEntity($targetEntity)
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function setBidirectional($bidirectional)
    {
        $this->bidirectional = $bidirectional;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isBidirectional()
    {
        return $this->bidirectional;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwningSide($owningSide)
    {
        $this->owningSide = $owningSide;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isOwningSide()
    {
        return $this->owningSide;
    }

    /**
     * {@inheritdoc}
     */
    public function setMappedBy($mappedBy)
    {
        $this->mappedBy = $mappedBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedBy()
    {
        return $this->mappedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setInversedBy($inversedBy)
    {
        $this->inversedBy = $inversedBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInversedBy()
    {
        return $this->inversedBy;
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

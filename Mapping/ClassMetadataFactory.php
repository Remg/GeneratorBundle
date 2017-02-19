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

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Remg\GeneratorBundle\Model\Association;
use Remg\GeneratorBundle\Model\EntityInterface;
use Remg\GeneratorBundle\Model\PrimaryKeyInterface;

/**
 * A disconnected ClassMetadataFactory.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class ClassMetadataFactory extends DisconnectedClassMetadataFactory implements ClassMetadataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createMetadataFor($className)
    {
        /* @var \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $this->newClassMetadataInstance($className);

        // Initialize name, namespace, and table name
        $this->initializeReflection($metadata, $this->getReflectionService());

        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFrom(EntityInterface $entity)
    {
        $metadata = $this->createMetadataFor($entity->getName());

        $builder = new ClassMetadataBuilder($metadata);

        $this->buildFields($entity, $builder);

        $this->buildAssociations($entity, $builder);

        return $metadata;
    }

    /**
     * Build field mapping informations with a given builder from a given
     * EntityInterface instance.
     *
     * @param EntityInterface      $entity
     * @param ClassMetadataBuilder $builder
     */
    private function buildFields(EntityInterface $entity, ClassMetadataBuilder $builder)
    {
        foreach ($entity->getFields() as $field) {
            if ($field instanceof PrimaryKeyInterface) {
                $builder
                    ->createField($field->getName(), $field->getType())
                    ->makePrimaryKey()
                    ->generatedValue()
                    ->build();
                continue;
            }

            $builder
                ->createField($field->getName(), $field->getType())
                ->nullable($field->isNullable())
                ->unique($field->isUnique())
                ->length($field->getLength())
                ->precision($field->getPrecision())
                ->scale($field->getScale())
                ->build();
        }
    }

    /**
     * Build association mapping informations with a given builder from a given
     * EntityInterface instance.
     *
     * @param EntityInterface      $entity
     * @param ClassMetadataBuilder $builder
     */
    private function buildAssociations(EntityInterface $entity, ClassMetadataBuilder $builder)
    {
        foreach ($entity->getAssociations() as $association) {
            switch ($association->getType()) {
                case Association::ONE_TO_ONE:
                    $associationBuilder = $builder
                        ->createOneToOne(
                            $association->getName(),
                            $association->getTargetEntity()
                        )
                        ->inversedBy($association->getInversedBy())
                        ->mappedBy($association->getMappedBy());
                    break;
                case Association::ONE_TO_MANY:
                    /* @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table */
                    if (!$association->isBidirectional()) {
                        $associationBuilder = $builder
                            ->createManyToMany(
                                $association->getName(),
                                $association->getTargetEntity()
                            )
                            ->addInverseJoinColumn(null, null, false, true);
                        break;
                    }

                    $associationBuilder = $builder
                        ->createOneToMany(
                            $association->getName(),
                            $association->getTargetEntity()
                        )
                        ->mappedBy($association->getMappedBy());
                    break;
                case Association::MANY_TO_ONE:
                    $associationBuilder = $builder
                        ->createManyToOne(
                            $association->getName(),
                            $association->getTargetEntity()
                        )
                        ->inversedBy($association->getInversedBy());
                    break;
                case Association::MANY_TO_MANY:
                    $associationBuilder = $builder
                        ->createManyToMany(
                            $association->getName(),
                            $association->getTargetEntity()
                        )
                        ->inversedBy($association->getInversedBy())
                        ->mappedBy($association->getMappedBy());
                    break;
            }

            $associationBuilder->build();
        }
    }
}

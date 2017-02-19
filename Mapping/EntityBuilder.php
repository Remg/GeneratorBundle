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

use Doctrine\ORM\Mapping\ClassMetadata;
use Remg\GeneratorBundle\Model\Association;
use Remg\GeneratorBundle\Model\AssociationInterface;
use Remg\GeneratorBundle\Model\Entity;
use Remg\GeneratorBundle\Model\EntityInterface;
use Remg\GeneratorBundle\Model\Field;
use Remg\GeneratorBundle\Model\PrimaryKey;

/**
 * The EntityBuilder creates and configures Entity instances.
 * It is also used to convert ClassMetadata into Entity.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityBuilder implements EntityBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function newEntityInstance($name)
    {
        $entity = new Entity();
        $entity->setName($name);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function addPrimaryKey(EntityInterface $entity, $name = 'id')
    {
        $primaryKey = new PrimaryKey();
        $primaryKey->setName($name);

        $entity->addField($primaryKey);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function discoverAssociations(EntityInterface $entity, array $entities)
    {
        foreach ($entities as $targetEntity) {
            $targetAssociations = $targetEntity
                ->getAssociations()
                ->filter(function (AssociationInterface $association) use ($entity) {
                    return $association->getTargetEntity() === $entity->getName();
                });

            foreach ($targetAssociations as $targetAssociation) {
                if (!$targetAssociation->isBidirectional()) {
                    continue;
                }

                $association = $this->getReverseAssociation($targetAssociation);

                $entity->addAssociation($association);
            }
        }

        return $this;
    }

    /**
     * Creates the reverse Association of a given Association.
     *
     * @param AssociationInterface $targetAssociation The association to reverse.
     *
     * @return Association
     */
    private function getReverseAssociation(AssociationInterface $targetAssociation)
    {
        $name = $targetAssociation->isOwningSide()
            ? $targetAssociation->getInversedBy()
            : $targetAssociation->getMappedBy();

        switch ($targetAssociation->getType()) {
            case Association::ONE_TO_ONE:
                $type = Association::ONE_TO_ONE;
                break;
            case Association::MANY_TO_ONE:
                $type = Association::ONE_TO_MANY;
                break;
            case Association::ONE_TO_MANY:
                $type = Association::MANY_TO_ONE;
                break;
            case Association::MANY_TO_MANY:
                $type = Association::MANY_TO_MANY;
                break;
        }

        $targetEntity = $targetAssociation->getEntity()->getName();

        // We are actually reversing a bidirectional association.
        $bidirectional = true;

        $owningSide = !$targetAssociation->isOwningSide();

        $mappedBy = $targetAssociation->isOwningSide()
            ? $targetAssociation->getName()
            : null;

        $inversedBy = $targetAssociation->isOwningSide()
            ? null
            : $targetAssociation->getName();

        $association = new Association();
        $association
            ->setName($name)
            ->setType($type)
            ->setTargetEntity($targetEntity)
            ->setBidirectional($bidirectional)
            ->setOwningSide($owningSide)
            ->setMappedBy($mappedBy)
            ->setInversedBy($inversedBy);

        return $association;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityFrom(ClassMetadata $metadata)
    {
        $entity = $this->newEntityInstance($metadata->getName());

        foreach ($metadata->getFieldNames() as $name) {
            $field = $this->getField($metadata, $name);

            $entity->addField($field);
        }

        foreach ($metadata->getAssociationNames() as $name) {
            $association = $this->getAssociation($metadata, $name);

            $entity->addAssociation($association);
        }

        return $entity;
    }

    /**
     * Creates a new FieldInterface instance for a given ClassMetadata and
     * field name.
     *
     * @param ClassMetadata $metadata The class metadata descriptor.
     * @param string        $name     The field name.
     *
     * @return Field The converted Field.
     */
    private function getField(ClassMetadata $metadata, $name)
    {
        $mapping = $metadata->getFieldMapping($name);

        if ($metadata->isIdentifier($name)) {
            return new PrimaryKey($mapping);
        }

        return new Field($mapping);
    }

    /**
     * Creates a new Association instance for a given ClassMetadata and
     * association name.
     *
     * @param ClassMetadata $metadata The class metadata descriptor.
     * @param string        $name     The associaion name.
     *
     * @return Association The converted Association.
     */
    private function getAssociation(ClassMetadata $metadata, $name)
    {
        $mapping = $metadata->getAssociationMapping($name);

        return new Association($mapping);
    }
}

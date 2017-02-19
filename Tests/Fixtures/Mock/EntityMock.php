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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Mockery for the Entity class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait EntityMock
{
    /**
     * Creates a mocked EntityInterface.
     *
     * @param string                 $name         The entity name.
     * @param FieldInterface[]       $fields       An array of fields indexed by name.
     * @param AssociationInterface[] $associations An array of associations indexed by name.
     *
     * @return Entity A mocked EntityInterface.
     */
    protected function getEntity(
        $name = null,
        array $fields = [],
        array $associations = []
    ) {
        $entity = $this
            ->getMockBuilder('Remg\GeneratorBundle\Model\EntityInterface')
            ->getMock();

        if (null !== $name) {
            $entity
                ->method('getName')
                ->willReturn($name);
        }

        // Fields
        $collection = new ArrayCollection();
        foreach ($fields as $field) {
            $collection->add($field);

            $field
                ->method('getEntity')
                ->willReturn($entity);
        }

        $entity
            ->method('getFields')
            ->willReturn($collection);

        $map = [];
        foreach ($fields as $fieldName => $field) {
            $map[] = [$fieldName, $field];
        }
        $entity
            ->method('getField')
            ->will($this->returnValueMap($map));

        $map = [];
        foreach (array_keys($fields) as $fieldName) {
            $map[] = [$fieldName, true];
        }
        $entity
            ->method('hasField')
            ->will($this->returnValueMap($map));

        // Associations
        $collection = new ArrayCollection();
        foreach ($associations as $association) {
            $collection->add($association);

            $association
                ->method('getEntity')
                ->willReturn($entity);
        }

        $entity
            ->method('getAssociations')
            ->willReturn($collection);

        $map = [];
        foreach ($associations as $fieldName => $association) {
            $map[] = [$fieldName, $association];
        }
        $entity
            ->method('getAssociation')
            ->will($this->returnValueMap($map));

        $map = [];
        foreach (array_keys($associations) as $fieldName) {
            $map[] = [$fieldName, true];
        }
        $entity
            ->method('hasAssociation')
            ->will($this->returnValueMap($map));

        return $entity;
    }
}

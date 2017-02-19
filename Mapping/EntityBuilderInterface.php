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
use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * Contract for an entity builder class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface EntityBuilderInterface
{
    /**
     * Creates a new Entity instance for the given class name.
     *
     * @param string $name The entity fully qualified class name.
     *
     * @return Entity
     */
    public function newEntityInstance($name);

    /**
     * Adds a primary key to the given entity.
     *
     * @param Entity $entity The entity being built.
     * @param string $name   The primary key field name.
     *
     * @return self
     */
    public function addPrimaryKey(EntityInterface $entity, $name = 'id');

    /**
     * Discovers and maps all associations targetting the given Entity.
     *
     * @param Entity   $entity   The entity being built.
     * @param Entity[] $entities The entities to inspect.
     *
     * @return self
     */
    public function discoverAssociations(EntityInterface $entity, array $entities);

    /**
     * Converts a ClassMetadata into an Entity.
     *
     * @param ClassMetadata $metadata The class metadata descriptor.
     *
     * @return Entity The converted Entity.
     */
    public function getEntityFrom(ClassMetadata $metadata);
}

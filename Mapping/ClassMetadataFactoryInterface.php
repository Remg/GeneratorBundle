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

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as BaseInterface;
use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * Contract for a disconnected class metadata factory to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface ClassMetadataFactoryInterface extends BaseInterface
{
    /**
     * Creates a new ClassMetadata instance for the given class name.
     *
     * @param string $className
     *
     * @return ClassMetadata
     */
    public function createMetadataFor($className);

    /**
     * Converts an EntityInterface instance into a ClassMetadata instance.
     *
     * @param EntityInterface $entity
     *
     * @return ClassMetadata
     */
    public function getMetadataFrom(EntityInterface $entity);
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Generator;

use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * Contract for an entity generator class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface EntityGeneratorInterface
{
    /**
     * Dumps an entity class file from a given EntityInterface.
     *
     * @param EntityInterface $entity
     */
    public function dump(EntityInterface $entity);
}

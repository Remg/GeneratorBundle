<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Command\Helper;

use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * A helper to interact with the user to define an Entity.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface EntityHelperInterface
{
    /**
     * Asks an entity name.
     *
     * @param string $default The default name.
     *
     * @return The answered name.
     */
    public function askName($default = self::DEFAULT_NAME);

    /**
     * Interaction flow to define an EntityInterface.
     *
     * @param EntityInterface $entity The entity to define.
     *
     * @return EntityInterface The defined entity.
     */
    public function askEntity(EntityInterface $entity);
}

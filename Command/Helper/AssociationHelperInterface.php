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

use Remg\GeneratorBundle\Model\AssociationInterface;

/**
 * Contract for an association helper class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface AssociationHelperInterface
{
    /**
     * Questions flow to ask all the informations about one association.
     *
     * NOTE: Some questions does not need interaction with the user and are
     * answered with logic.
     *
     * @param AssociationInterface $association The association to define.
     *
     * @return AssociationInterface The defined association.
     */
    public function askAssociation(AssociationInterface $association);
}

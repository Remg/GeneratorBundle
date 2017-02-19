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

use Remg\GeneratorBundle\Model\FieldInterface;

/**
 * Contract for a field helper class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface FieldHelperInterface
{
    /**
     * Questions flow to ask all the informations about one field.
     *
     * NOTE: Some questions does not need interaction with the user and are
     * answered with logic.
     *
     * @param FieldInterface $field The field to define.
     *
     * @return FieldInterface The defined field.
     */
    public function askField(FieldInterface $field);
}

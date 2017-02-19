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

use Symfony\Component\Console\Style\StyleInterface;

/**
 * Contract for a style aware class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface StyleAwareInterface
{
    /**
     * Sets the StyleInterface to the current instance.
     *
     * @param StyleInterface $display The StyleInterface.
     *
     * @return self
     */
    public function setDisplay(StyleInterface $display);
}

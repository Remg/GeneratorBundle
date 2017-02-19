<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tools;

/**
 * Contract for a bundle manager class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface BundleManagerInterface
{
    /**
     * Returns whether the given bundle is enabled.
     *
     * @param string $name The name of the bundle.
     *
     * @return bool Whether the bundle is enabled.
     */
    public function hasBundle($name);

    /**
     * Returns a bundle by name.
     *
     * @param string $name The name of the bundle to return.
     *
     * @throws BundleNotFoundException If the bundle can not be retrieved.
     *
     * @return Symfony\Component\HttpKernel\Bundle\BundleInterface The bundle
     */
    public function getBundle($name);
}

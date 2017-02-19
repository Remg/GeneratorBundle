<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Fixtures\Provider;

use Symfony\Component\Yaml\Yaml;

/**
 * Provider related to bundles.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait BundleProvider
{
    /**
     * Provides test cases for bundles.
     *
     * @return array[
     *                [
     *                0 => The bundle namespace.
     *                1 => The bundle name.
     *                ]
     */
    public function bundleProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/Bundle.yml'));
    }
}

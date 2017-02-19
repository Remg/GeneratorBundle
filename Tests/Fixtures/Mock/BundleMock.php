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

/**
 * Mockery for the BundleInterface class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait BundleMock
{
    /**
     * Creates a mocked BundleInterface.
     *
     * @param string $name      The bundle name.
     * @param string $namespace The bundle namespace.
     * @param string $path      The bundle absolute path.
     *
     * @return Symfony\Component\HttpKernel\Bundle\BundleInterface
     */
    protected function getBundle($name = null, $namespace = null, $path = null)
    {
        $bundle = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')
            ->getMock();

        if (null !== $name) {
            $bundle
                ->method('getName')
                ->willReturn($name);
        }
        if (null !== $namespace) {
            $bundle
                ->method('getNamespace')
                ->willReturn($namespace);
        }
        if (null !== $path) {
            $bundle
                ->method('getPath')
                ->willReturn($path);
        }

        return $bundle;
    }
}

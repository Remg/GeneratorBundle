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
trait BundleManagerMock
{
    /**
     * Creates a mocked BundleInterface.
     *
     * @return Remg\GeneratorBundle\Tools\BundleManager
     */
    protected function getBundleManager()
    {
        $bundleManager = $this
            ->getMockBuilder('Remg\GeneratorBundle\Tools\BundleManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        return $bundleManager;
    }
}

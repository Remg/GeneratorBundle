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
 * Mockery for the BundleNotFoundException class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait BundleNotFoundExceptionMock
{
    /**
     * Creates a mocked BundleNotFoundException.
     *
     * @return Remg\GeneratorBundle\Exception\BundleNotFoundException
     */
    protected function getBundleNotFoundException()
    {
        $exception = $this
            ->getMockBuilder('Remg\GeneratorBundle\Exception\BundleNotFoundException')
            ->disableOriginalConstructor()
            ->getMock();

        return $exception;
    }
}

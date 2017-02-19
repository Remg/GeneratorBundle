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
 * Mockery for the MappingException class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait MappingExceptionMock
{
    /**
     * Creates a mocked MappingException.
     *
     * @return Remg\GeneratorBundle\Exception\MappingException
     */
    protected function getMappingException()
    {
        $exception = $this
            ->getMockBuilder('Remg\GeneratorBundle\Exception\MappingException')
            ->disableOriginalConstructor()
            ->getMock();

        return $exception;
    }
}

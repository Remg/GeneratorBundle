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
 * Mockery for the EntityBuilder class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait EntityBuilderMock
{
    /**
     * Creates a mocked EntityBuilderInterface.
     *
     * @return EntityBuilderInterface A mocked EntityBuilderInterface.
     */
    protected function getEntityBuilder()
    {
        $entityBuilder = $this
            ->getMockBuilder('Remg\GeneratorBundle\Mapping\EntityBuilderInterface')
            ->getMock();

        return $entityBuilder;
    }
}

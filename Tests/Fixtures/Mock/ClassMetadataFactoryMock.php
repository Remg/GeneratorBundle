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
 * Mockery for the ClassMetadataFactory class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait ClassMetadataFactoryMock
{
    /**
     * Creates a mocked ClassMetadataFactoryInterface.
     *
     * @return ClassMetadataFactoryInterface The mocked ClassMetadataFactoryInterface.
     */
    protected function getClassMetadataFactory()
    {
        return $this
            ->getMockBuilder('Remg\GeneratorBundle\Mapping\ClassMetadataFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

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
 * Mockery for the EntityFactory class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait EntityFactoryMock
{
    /**
     * Creates a mocked EntityFactoryInterface.
     *
     * @return EntityFactoryInterface A mocked EntityFactoryInterface.
     */
    protected function getEntityFactory()
    {
        $factory = $this
            ->getMockBuilder('Remg\GeneratorBundle\Mapping\EntityFactoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $keywordList = $this
            ->getMockBuilder('Doctrine\DBAL\Platforms\Keywords\KeywordList')
            ->getMock();

        $factory
            ->method('getPlatformKeywordList')
            ->willReturn($keywordList);

        return $factory;
    }
}

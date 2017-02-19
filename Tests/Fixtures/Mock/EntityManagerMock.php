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
 * Mockery for the EntityManager class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait EntityManagerMock
{
    /**
     * Creates a mocked EntityManager with mocked dependencies.
     *
     * @return EntityManager A mocked EntityManager.
     */
    protected function getEntityManager()
    {
        $connection = $this
            ->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $databasePlatform = $this
            // Use MySQL57Platform since the AbstractPlatform::getReservedKeywordList
            // method is final.
            ->getMockBuilder('Doctrine\DBAL\Platforms\MySQL57Platform')
            ->getMock();

        $keywordList = $this
            ->getMockBuilder('Doctrine\DBAL\Platforms\Keywords\KeywordList')
            ->getMock();

        $namingStrategy = $this
            ->getMockBuilder('Doctrine\ORM\Mapping\NamingStrategy')
            ->disableOriginalConstructor()
            ->getMock();

        $configuration = $this
            ->getMockBuilder('Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $driver = $this
            ->getMockBuilder('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain')
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = $this
            ->getMockBuilder('Doctrine\Common\EventManager')
            ->disableOriginalConstructor()
            ->getMock();

        $metadataFactory = $this
            ->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $databasePlatform
            ->method('getReservedKeywordsList')
            ->willReturn($keywordList);

        $connection
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatform);

        $configuration
            ->method('getNamingStrategy')
            ->willReturn($namingStrategy);
        $configuration
            ->method('getMetadataDriverImpl')
            ->willReturn($driver);

        $namingStrategy
            ->method('referenceColumnName')
            ->willReturn('id');

        $entityManager
            ->method('getConnection')
            ->willReturn($connection);
        $entityManager
            ->method('getConfiguration')
            ->willReturn($configuration);
        $entityManager
            ->method('getEventManager')
            ->willReturn($eventManager);
        $entityManager
            ->method('getMetadataFactory')
            ->willReturn($metadataFactory);

        return $entityManager;
    }
}

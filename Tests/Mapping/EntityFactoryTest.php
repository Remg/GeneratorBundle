<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Mapping\EntityFactory;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the EntityFactory class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityFactoryTest extends TestCase
{
    use Fixtures\Mock\BundleMock,
        Fixtures\Mock\BundleManagerMock,
        Fixtures\Mock\ClassMetadataMock,
        Fixtures\Mock\ClassMetadataFactoryMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Mock\EntityBuilderMock,
        Fixtures\Mock\EntityManagerMock,
        Fixtures\Mock\PrimaryKeyMock,
        Fixtures\Provider\EntityProvider;

    /**
     * Contains a mocked EntityManager instance.
     */
    private $entityManager;

    /**
     * Contains a mocked BundleManagerInterface instance.
     */
    private $bundleManager;

    /**
     * Contains a mocked ClassMetadataFactoryInstance instance.
     */
    private $metadataFactory;

    /**
     * Contains a mocked EntityBuilderInterface instance.
     */
    private $entityBuilder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->entityManager = $this->getEntityManager();
        $this->bundleManager = $this->getBundleManager();
        $this->metadataFactory = $this->getClassMetadataFactory();
        $this->entityBuilder = $this->getEntityBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->entityManager = null;
        $this->bundleManager = null;
        $this->metadataFactory = null;
        $this->entityBuilder = null;
        $this->entityFactory = null;
    }

    /**
     * Returns an EntityFactory instance.
     *
     * @return EntityFactory
     */
    public function getEntityFactory()
    {
        return new EntityFactory(
            $this->entityManager,
            $this->bundleManager,
            $this->metadataFactory,
            $this->entityBuilder
        );
    }

    /**
     * Tests the method "__construct()".
     */
    public function testConstruct()
    {
        $factory = $this->getEntityFactory();

        $this->assertEmpty($factory->getAllEntities(), 'The factory should not have any loaded Entity.');
    }

    /**
     * Tests the method "hasEntity()" with no registered entity.
     *
     * @dataProvider entityProvider
     */
    public function testHasNoEntity(array $mapping)
    {
        $factory = $this->getEntityFactory();

        $this->assertFalse($factory->hasEntity($mapping['name']), 'The factory should not have any loaded Entity.');
    }

    /**
     * Tests the method "hasEntity()" with a registered entity.
     *
     * @dataProvider entityProvider
     */
    public function testHasEntity(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(true);
        $this
            ->bundleManager
            ->method('getBundle')
            ->willReturn($this->getBundle());

        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->willReturn(true);
        $this
            ->metadataFactory
            ->method('getMetadataFor')
            ->willreturn($metadata);

        $this
            ->entityBuilder
            ->method('getEntityFrom')
            ->willReturn($this->getEntity($mapping['name']));

        $factory = $this->getEntityFactory();

        $this->assertTrue($factory->hasEntity($mapping['name']), 'The factory should be aware of this Entity.');
    }

    /**
     * Tests the method "hasEntity()" with an unknown bundle.
     *
     * @dataProvider entityProvider
     */
    public function testHasEntityWithUnknownBundle(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(false);

        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->willReturn(true);
        $this
            ->metadataFactory
            ->method('getMetadataFor')
            ->willreturn($metadata);

        $this
            ->entityBuilder
            ->method('getEntityFrom')
            ->willReturn($this->getEntity($mapping['name']));

        $factory = $this->getEntityFactory();

        $this->assertFalse($factory->hasEntity($mapping['name']), 'The factory should not be aware of this Entity.');
    }

    /**
     * Tests the method "getEntity()" with no registered entity.
     *
     * @dataProvider entityProvider
     */
    public function testGetNoEntity(array $mapping)
    {
        $factory = $this->getEntityFactory();

        $this->expectException('Remg\GeneratorBundle\Exception\EntityNotFoundException');
        $this->expectExceptionMessage(sprintf(
            'The entity "%s" does not exist.',
            $mapping['name']
        ));

        $factory->getEntity($mapping['name']);
    }

    /**
     * Tests the method "getEntity()" with a registered entity.
     *
     * @dataProvider entityProvider
     */
    public function testGetEntity(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(true);
        $this
            ->bundleManager
            ->method('getBundle')
            ->willReturn($this->getBundle());

        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->willReturn(true);
        $this
            ->metadataFactory
            ->method('getMetadataFor')
            ->willreturn($metadata);

        $this
            ->entityBuilder
            ->method('getEntityFrom')
            ->willReturn($this->getEntity($mapping['name']));

        $factory = $this->getEntityFactory();

        $entity = $factory->getEntity($mapping['name']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\EntityInterface', $entity,
            'The method getEntity() should resturn Entity instances.');
        $this->assertEquals($mapping['name'], $entity->getName());
    }

    /**
     * Tests the method "getEntity()" with an unknown bundle.
     *
     * @dataProvider entityProvider
     */
    public function testGetEntityWithUnknownBundle(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(false);

        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->willReturn(true);
        $this
            ->metadataFactory
            ->method('getMetadataFor')
            ->willreturn($metadata);

        $this
            ->entityBuilder
            ->method('getEntityFrom')
            ->willReturn($this->getEntity($mapping['name']));

        $factory = $this->getEntityFactory();

        $this->expectException('Remg\GeneratorBundle\Exception\BundleNotFoundException');
        $this->expectExceptionMessage(sprintf(
            'The bundle of the entity "%s" can not be found.',
            $mapping['name']
        ));

        $factory->getEntity($mapping['name']);
    }

    /**
     * Tests the method "getAllEntities()".
     *
     * @dataProvider entityProvider
     */
    public function testGetAllEntities(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(true);
        $this
            ->bundleManager
            ->method('getBundle')
            ->willReturn($this->getBundle());

        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->willReturn(true);
        $this
            ->metadataFactory
            ->method('getMetadataFor')
            ->willreturn($metadata);
        $this
            ->metadataFactory
            ->method('getAllMetadata')
            ->willreturn([$metadata]);

        $this
            ->entityBuilder
            ->method('getEntityFrom')
            ->willReturn($this->getEntity($mapping['name']));

        $factory = $this->getEntityFactory();

        $this->assertCount(1, $factory->getAllEntities(), 'The entities array should contain one entity.');
    }

    /**
     * Tests the method "getAllEntities()" with registered entities with unknown bundle.
     *
     * @dataProvider entityProvider
     */
    public function testGetAllEntitiesWithUnknownBundle(array $mapping)
    {
        $this
            ->bundleManager
            ->method('hasBundle')
            ->willReturn(false);

        $factory = $this->getEntityFactory();

        $this->assertEmpty($factory->getAllEntities(), 'The factory should not have any loaded Entity.');
    }

    /**
     * Tests the method "getAllEntities()" with registered mapped superclass.
     *
     * @dataProvider entityProvider
     */
    public function testGetAllEntitiesWithMappedSuperclass(array $mapping)
    {
        $metadata = $this->getClassMetadata($mapping['name']);
        $metadata->isMappedSuperclass = true;

        $this
            ->metadataFactory
            ->method('getAllMetadata')
            ->willreturn([$metadata]);

        $factory = $this->getEntityFactory();

        $this->assertEmpty($factory->getAllEntities(), 'The factory should not have any loaded Entity.');
    }

    /**
     * Tests the method "getAllEntities()" with registered mapped superclass.
     *
     * @dataProvider entityProvider
     */
    public function testGetAllEntitiesWithEntityWithUnknownBundle(array $mapping)
    {
        $metadata = $this->getClassMetadata($mapping['name']);
        $this
            ->metadataFactory
            ->method('getAllMetadata')
            ->willreturn([$metadata]);
        $this
            ->metadataFactory
            ->method('hasMetadataFor')
            ->with($mapping['name'])
            ->willReturn(true);

        $this
            ->bundleManager
            ->method('hasBundle')
            ->with($mapping['name'])
            ->willReturn(false);

        $factory = $this->getEntityFactory();

        $this->assertEmpty($factory->getAllEntities(), 'The factory should not have any loaded Entity.');
    }

    /**
     * Tests the method "createEntity()".
     *
     * @dataProvider entityProvider
     */
    public function testCreateEntity(array $mapping)
    {
        $entity = $this->getEntity($mapping['name'], [
            'id' => $this->getPrimaryKey('id'),
        ]);
        $bundle = $this->getBundle();

        $entity
            ->method('getBundle')
            ->willReturn($bundle);

        $this
            ->bundleManager
            ->method('getBundle')
            ->willReturn($this->getBundle());

        $this
            ->entityBuilder
            ->method('newEntityInstance')
            ->willReturn($entity);

        $factory = $this->getEntityFactory();

        $entity = $factory->createEntity($mapping['name']);

        $this->assertInstanceOf('Remg\GeneratorBundle\Model\EntityInterface', $entity,
            'The method "createEntity()" should return Entity instances.');
        $this->assertEquals($mapping['name'], $entity->getName(), 'The entity name is wrong.');
        $this->assertEquals($bundle, $entity->getBundle(), 'The resolved bundle is wrong.');
        $this->assertCount(1, $entity->getFields(), 'The entity should have one mapped field.');
        $this->assertTrue($entity->hasField('id'), 'The entity should own a field named id.');

        $field = $entity->getField('id');
        $this->assertInstanceOf('Remg\GeneratorBundle\Model\PrimaryKeyInterface', $field,
            'The field named "id" should be a PrimaryKey instance.');

        $this->assertEmpty($entity->getAssociations(), 'The entity should not own any association.');
    }

    /**
     * Tests the method "getFqcnFromShortcut()".
     *
     * @dataProvider entityProvider
     */
    public function testGetFqcnFromShortcut(array $mapping)
    {
        $bundle = $this->getBundle($mapping['bundleName'], $mapping['bundleNamespace']);
        $this
            ->bundleManager
            ->method('getBundle')
            ->with($mapping['bundleName'])
            ->willReturn($bundle);

        $this
            ->entityManager
            ->getConfiguration()
            ->method('getEntityNamespace')
            ->with($mapping['bundleName'])
            ->willReturn($mapping['bundleNamespace'].'\\Entity');

        $factory = $this->getEntityFactory();

        $fqcn = $factory->getFqcnFromShortcut($mapping['shortcut']);

        $this->assertEquals($mapping['name'], $fqcn, 'The resolved fully qualified class name is wrong.');
    }

    /**
     * Tests the method "getPlatformKeywordList()".
     */
    public function testGetPlatformKeywordList()
    {
        $factory = $this->getEntityFactory();

        $this->assertInstanceOf(
            'Doctrine\DBAL\Platforms\Keywords\KeywordList',
            $factory->getPlatformKeywordList(),
            'The method "getPlatformKeywordList()" should return KeywordList instances.'
        );
    }
}

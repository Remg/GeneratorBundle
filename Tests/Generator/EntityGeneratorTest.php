<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Generator\EntityGenerator;
use Remg\GeneratorBundle\Tests\Fixtures;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Unit tests for the EntityGenerator class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityGeneratorTest extends TestCase
{
    use Fixtures\Mock\ClassMetadataMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Mock\ClassMetadataFactoryMock,
        Fixtures\Provider\EntityProvider;

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove(sys_get_temp_dir().'/phpunit/remg');
    }

    /**
     * Tests the method "dump".
     *
     * @dataProvider entityProvider
     */
    public function testDump(array $mapping)
    {
        $path = sys_get_temp_dir().'/phpunit/remg'.$mapping['entityPath'];
        $configurationPath = sys_get_temp_dir().'/phpunit/remg'.$mapping['configurationPath'];

        $entity = $this->getEntity($mapping['name']);
        $entity
            ->method('getPath')
            ->willReturn($path);
        $entity
            ->method('getConfigurationPath')
            ->willReturn($configurationPath);

        $metadata = $this->getClassMetadata($mapping['name']);

        $metadataFactory = $this->getClassMetadataFactory();
        $metadataFactory
            ->method('getMetadataFrom')
            ->willReturn($metadata);

        $generator = new EntityGenerator($metadataFactory, [
            'configuration_format' => $mapping['format'],
        ]);

        $generator->dump($entity);

        $this->assertFileExists($path, 'The file should exist.');

        if (isset($mapping['configurationPath'])) {
            $this->assertFileExists($configurationPath, 'The configuration file should exist.');
        }
    }
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\DependencyInjection\RemgGeneratorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Unit tests for the RemgGeneratorExtension class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class RemgGeneratorExtensionTest extends TestCase
{
    /**
     * Tests the method "load".
     */
    public function testLoad()
    {
        $container = new ContainerBuilder();

        $extension = new RemgGeneratorExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('remg_generator.kernel_bundles'));
        $this->assertTrue($container->hasDefinition('remg_generator.bundle_manager'));
        $this->assertTrue($container->hasDefinition('remg_generator.entity_factory'));
        $this->assertTrue($container->hasDefinition('remg_generator.mapping_validator'));
        $this->assertTrue($container->hasDefinition('remg_generator.mapping_guesser'));
        $this->assertTrue($container->hasDefinition('remg_generator.entity_generator'));
        $this->assertTrue($container->hasDefinition('remg_generator.entity_helper'));
    }
}

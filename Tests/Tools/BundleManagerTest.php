<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Tools;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Tests\Fixtures;
use Remg\GeneratorBundle\Tools\BundleManager;

/**
 * Unit tests for the BundleManager class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class BundleManagerTest extends TestCase
{
    use Fixtures\Mock\BundleMock,
        Fixtures\Provider\BundleProvider;

    /**
     * Tests the method "hasBundle()".
     *
     * @dataProvider bundleProvider
     */
    public function testHasBundle($name, $namespace)
    {
        $bundles = [
            $name => $this->getBundle($name, $namespace),
        ];

        $manager = new BundleManager($bundles);

        $this->assertTrue($manager->hasBundle($name), 'The bundle should exist.');
        $this->assertTrue($manager->hasBundle($namespace), 'The bundle should exist.');
    }

    /**
     * Tests the method "hasBundle()".
     *
     * @dataProvider bundleProvider
     */
    public function testHasNoBundle($name, $namespace)
    {
        $bundles = [];

        $manager = new BundleManager($bundles);

        $this->assertFalse($manager->hasBundle($name), 'The bundle should not exist.');
        $this->assertFalse($manager->hasBundle($namespace), 'The bundle should not exist.');
    }

    /**
     * Tests the method "getBundle".
     *
     * @dataProvider bundleProvider
     */
    public function testGetBundle($name, $namespace)
    {
        $bundle = $this->getBundle($namespace, $name);

        $manager = new BundleManager([$name => $bundle]);

        $this->assertInstanceOf(
            'Symfony\Component\HttpKernel\Bundle\BundleInterface',
            $manager->getBundle($name),
            'The manager should return a BundleInterface.'
        );
        $this->assertEquals($bundle, $manager->getBundle($name), 'The expected bundle does not match the bundle resolved by name.');
        $this->assertEquals($bundle, $manager->getBundle($namespace), 'The expected bundle does not match the bundle resolved by namespace.');
    }

    /**
     * Tests that the method "getBundle" throws an exception if the
     * given bundle name is not known.
     *
     * @dataProvider      bundleProvider
     * @expectedException \Remg\GeneratorBundle\Exception\BundleNotFoundException
     */
    public function testBundleNotFound($name)
    {
        $manager = new BundleManager([]);

        $manager->getBundle($name);
    }
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Unit tests for the RegenerateEntityCommand class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class RegenerateEntityCommandTest extends KernelTestCase
{
    /**
     * Contains an instance of a Filesystem.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->filesystem = new Filesystem();

        $entityDirectory = __DIR__.'/../../Entity';

        $this->filesystem->mkdir($entityDirectory);
        $this->filesystem->copy(
            __DIR__.'/../app/CustomEntity.php',
            $entityDirectory.'/CustomEntity.php'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->filesystem->remove(__DIR__.'/../../Entity');

        $this->filesystem = null;
    }

    /**
     * Tests the command in "interactive" mode.
     */
    public function testDefaultInteractive()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('remg:regenerate:entity');
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'Remg\GeneratorBundle\Entity\CustomEntity',
            // add a field ?
            'yes',
                'name',
                'string',
                -1, 255,
                'no',
                'yes',
            // add a field ?
            'yes',
                'price',
                'decimal',
                -1, 100, 10,
                -1, 50, 12, 2,
                'no',
                'yes',
            // add a field ?
            'no',
            // add an association ?
            'yes',
                'image',
                'OneToOne',
                'RemgGeneratorBundle:Image',
                'yes',
                'yes',
                'post',
            // add an association ?
            'yes',
                'author',
                'ManyToOne',
                'RemgGeneratorBundle:Author',
                'no',
            // add an association ?
            'yes',
                'comments',
                'OneToMany',
                'RemgGeneratorBundle:Comment',
                'yes',
                'post',
            // add an association ?
            'yes',
                'parent',
                'ManyToOne',
                'RemgGeneratorBundle:Post',
                'yes',
                'posts',
            // add an association ?
            'yes',
                'tags',
                'ManyToMany',
                'RemgGeneratorBundle:Tag',
                'yes',
                'yes',
                'posts',
            // add an association ?
            'no',
            // edit before generation ?
            'yes',
            // edit a field ?
            'yes',
                // select field name
                'name',
                    'name',
                    'string',
                    255,
                    'no',
                    'yes',
            // edit field ?
            'None, skip this step.',
            // add field ?
            'no',
            // edit association ?
            'yes',
                // select association name
                'comments',
                    'comments',
                    'OneToMany',
                    'RemgGeneratorBundle:Comment',
                    'yes',
                    'post',
            // edit association ?
            'None, skip this step.',
            // add association ?
            'no',
            // edit before generation ?
            'no',
        ]);

        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertFileExists(__DIR__.'/../../Entity/CustomEntity.php');
        $file = file_get_contents(__DIR__.'/../../Entity/CustomEntity.php');
        $this->assertRegexp('/private \$name;/', $file);
    }
}

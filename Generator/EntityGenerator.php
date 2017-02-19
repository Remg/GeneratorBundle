<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Generator;

use Doctrine\ORM\Tools\EntityGenerator as DoctrineEntityGenerator;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Remg\GeneratorBundle\Mapping\ClassMetadataFactoryInterface;
use Remg\GeneratorBundle\Model\EntityInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * A generator that can dump files related to entities from a given
 * EntityInterface.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityGenerator implements EntityGeneratorInterface
{
    /**
     * Contains a ClassMetadataFactoryInterface instance.
     *
     * @var ClassMetadataFactoryInterface;
     */
    private $metadataFactory;

    /**
     * Contains a DoctrineEntityGenerator instance.
     *
     * @var DoctrineEntityGenerator
     */
    private $classGenerator;

    /**
     * Contai5ns the configuration parmaeters.
     *
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param ClassMetadataFactoryInterface $metadataFactory
     * @param array                         $parameters
     */
    public function __construct(ClassMetadataFactoryInterface $metadataFactory, array $parameters)
    {
        $this->metadataFactory = $metadataFactory;
        $this->parameters = $parameters;

        $this->filesystem = new FileSystem();
    }

    /**
     * {@inheritdoc}
     */
    public function dump(EntityInterface $entity)
    {
        $this->generateClass($entity);
        $this->generateConfiguration($entity);
    }

    /**
     * Generates the code of an entity class from a given EntityInterface.
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    private function generateClass(EntityInterface $entity)
    {
        $generator = $this->getClassGenerator();

        if ('annotation' !== $this->getParameter('configuration_format')) {
            $generator->setGenerateAnnotations(false);
        }

        $metadata = $this->metadataFactory->getMetadataFrom($entity);

        $this->dumpFile(
            $entity->getPath(),
            $generator->generateEntityClass($metadata)
        );
    }

    private function generateConfiguration(EntityInterface $entity)
    {
        $format = $this->getParameter('configuration_format');

        if ('annotation' === $format) {
            return;
        }

        $metadata = $this->metadataFactory->getMetadataFrom($entity);

        $metadataExporter = new ClassMetadataExporter();
        $exporter = $metadataExporter->getExporter($format);

        $this->dumpFile(
            $entity->getConfigurationPath($format),
            $exporter->exportClassMetadata($metadata)
        );
    }

    /**
     * Gets a configured Doctrine EntityGenerator instance.
     *
     * @return DoctrineEntityGenerator
     */
    private function getClassGenerator()
    {
        if (!$this->classGenerator) {
            $generator = new DoctrineEntityGenerator();
            $generator->setGenerateAnnotations(true);
            $generator->setGenerateStubMethods(true);
            $generator->setRegenerateEntityIfExists(true);
            $generator->setUpdateEntityIfExists(true);

            $this->classGenerator = $generator;
        }

        return $this->classGenerator;
    }

    /**
     * Gets a configuration parameter by name.
     *
     * @param string $name The parameter key.
     *
     * @return mixed The parameter value.
     */
    private function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Dumps a file.
     *
     * @param string $path    The file path to dump.
     * @param string $content The file content to dump.
     */
    private function dumpFile($path, $content)
    {
        // Backup existing file
        if ($this->filesystem->exists($path)) {
            $this->filesystem->copy(
                $path,
                $path.'~'.date('Y-m-d_H-i-s')
            );
        }

        $this->filesystem->dumpFile(
            $path,
            $content
        );
    }
}

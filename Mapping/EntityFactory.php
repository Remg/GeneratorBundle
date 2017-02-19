<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Mapping;

use Doctrine\ORM\EntityManager;
use Remg\GeneratorBundle\Exception\BundleNotFoundException;
use Remg\GeneratorBundle\Exception\EntityNotFoundException;
use Remg\GeneratorBundle\Tools\BundleManagerInterface;

/**
 * The EntityFactory is the central access point to Entity models.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityFactory implements EntityFactoryInterface
{
    /**
     * Contains an EntityManager instance.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Contains an BundleManagerInterface instance.
     *
     * @var BundleManagerInterface
     */
    private $bundleManager;

    /**
     * Contains an ClassMetadataFactoryInterface instance.
     *
     * @var ClassMetadataFactoryInterface;
     */
    private $metadataFactory;

    /**
     * Contains an EntityBuilderInterface instance.
     *
     * @var EntityBuilderInterface;
     */
    private $entityBuilder;

    /**
     * Contains all the already-loaded EntityInterface instances.
     *
     * @var \Remg\GeneratorBundle\Model\EntityInterface[]
     */
    private $entities = [];

    /**
     * Creates a new EntityFactory that operates with the given EntityManager
     * and BundleManagerInterface.
     *
     * @param EntityManager                 $entityManager
     * @param BundleManagerInterface        $bundleManager
     * @param ClassMetadataFactoryInterface $metadataFactory
     * @param EntityBuilderInterface        $entityBuilder
     */
    public function __construct(EntityManager $entityManager, BundleManagerInterface $bundleManager, ClassMetadataFactoryInterface $metadataFactory, EntityBuilderInterface $entityBuilder)
    {
        $this->entityManager = $entityManager;
        $this->bundleManager = $bundleManager;
        $this->metadataFactory = $metadataFactory;
        $this->entityBuilder = $entityBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function hasEntity($name)
    {
        try {
            $this->getEntity($name);
        } catch (EntityNotFoundException $exception) {
            return false;
        } catch (BundleNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllEntities()
    {
        $metadatas = $this->metadataFactory->getAllMetadata();

        if (empty($metadatas)) {
            return $this->entities;
        }

        foreach ($metadatas as $metadata) {
            if ($metadata->isMappedSuperclass) {
                continue;
            }

            $name = $metadata->getName();

            if (!$this->bundleManager->hasBundle($name)) {
                continue;
            }

            $entity = $this->entityBuilder->getEntityFrom($metadata);
            $entity->setBundle($this->getBundle($name));

            $this->entities[$name] = $entity;
        }

        return $this->entities;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($name)
    {
        if (!isset($this->entities[$name])) {
            /* @throws EntityNotFoundException */
            /* @throws BundleNotFoundException */
            $this->entities[$name] = $this->findEntity($name);
        }

        return $this->entities[$name];
    }

    /**
     * Finds an entity.
     *
     * @param string $name The entity name to find.
     *
     * @throws EntityNotFoundException If the entity metadata can not be found.
     * @throws BundleNotFoundException If the owning bundle can not be found.
     *
     * @return EntityInterface The resolved EntityInterface.
     */
    private function findEntity($name)
    {
        if (!$this->metadataFactory->hasMetadataFor($name)) {
            throw new EntityNotFoundException(sprintf(
                'The entity "%s" does not exist.', $name
            ));
        }

        if (!$this->bundleManager->hasBundle($name)) {
            throw new BundleNotFoundException(sprintf(
                'The bundle of the entity "%s" can not be found.', $name
            ));
        }

        $metadata = $this->metadataFactory->getMetadataFor($name);

        $entity = $this->entityBuilder->getEntityFrom($metadata);
        $entity->setBundle($this->getBundle($name));

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function createEntity($name)
    {
        $entity = $this->entityBuilder->newEntityInstance($name);
        $entity->setBundle($this->getBundle($name));

        $this
            ->entityBuilder
            ->addPrimaryKey($entity, $this->getReferenceColumnName());
        $this
            ->entityBuilder
            ->discoverAssociations($entity, $this->getAllEntities());

        return $entity;
    }

    /**
     * Gets the default reference column name for primary keys.
     *
     * @return string
     */
    private function getReferenceColumnName()
    {
        return $this
            ->entityManager
            ->getConfiguration()
            ->getNamingStrategy()
            ->referenceColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function getBundle($name)
    {
        $bundle = $this->bundleManager->getBundle($name);

        // Registers a bundle entity namespace in the entity manager configuration.
        // Needed for bundles that doesn't contain any entity yet.
        $entityNamespaces = $this
            ->entityManager
            ->getConfiguration()
            ->getEntityNamespaces();

        if (!isset($entityNamespaces[$bundle->getName()])) {
            $this
                ->entityManager
                ->getConfiguration()
                ->addEntityNamespace(
                    $bundle->getName(),
                    $bundle->getNamespace().'\\Entity'
                );
        }

        return $bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getFqcnFromShortcut($shortcut)
    {
        list($namespaceAlias, $simpleClassName) = explode(':', $shortcut, 2);

        $bundle = $this->getBundle($namespaceAlias);

        return $this
            ->entityManager
            ->getConfiguration()
            ->getEntityNamespace($bundle->getName()).'\\'.$simpleClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatformKeywordList()
    {
        return $this
            ->entityManager
            ->getConnection()
            ->getDatabasePlatform()
            ->getReservedKeywordsList();
    }
}

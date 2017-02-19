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

/**
 * Contract for an entity factory class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface EntityFactoryInterface
{
    /**
     * Checks whether the entity is known.
     *
     * @param string $name The name of the entity.
     *
     * @return bool Whether the entity is known.
     */
    public function hasEntity($name);

    /**
     * Forces the factory to load all known entities, and returns them.
     *
     * @return \Remg\GeneratorBundle\Model\EntityInterface[] An array of EntityInterface.
     */
    public function getAllEntities();

    /**
     * Gets an entity descriptor for a name.
     *
     * @param string $name
     *
     * @throws \Remg\GeneratorBundle\Exception\BundleNotFoundException
     *
     * @return \Remg\GeneratorBundle\Model\EntityInterface
     */
    public function getEntity($name);

    /**
     * Creates a new EntityInterface instance for the given name.
     *
     * @param string $name The entity name.
     *
     * @return \Remg\GeneratorBundle\Model\EntityInterface
     */
    public function createEntity($name);

    /**
     * Gets the BundleInterface for a name or namespace.
     *
     * @param string $name The bundle name or namespace.
     *
     * @throws \Remg\GeneratorBundle\Exception\BundleNotFoundException
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface
     */
    public function getBundle($name);

    /**
     * Gets the fully qualified class name from the entity shortcut notation.
     *
     * @param string $shortcut
     *
     * @return string
     */
    public function getFqcnFromShortcut($shortcut);

    /**
     * Gets the database platform reserved keyword list
     * resolved by the EntityManager.
     *
     * @return \Doctrine\DBAL\Platforms\Keywords\KeywordList
     */
    public function getPlatformKeywordList();
}

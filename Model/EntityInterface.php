<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Model;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Contract for an entity model class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface EntityInterface
{
    /**
     * Sets the entity name.
     *
     * @param string $name
     *
     * @return EntityInterface
     */
    public function setName($name);

    /**
     * Gets the entity name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the entity bundle.
     *
     * @param BundleInterface $bundle
     *
     * @return EntityInterface
     */
    public function setBundle(BundleInterface $bundle);

    /**
     * Gets the entity bundle.
     *
     * @return BundleInterface
     */
    public function getBundle();

    /**
     * Adds a field to the entity.
     *
     * @param FieldInterface $field
     *
     * @return EntityInterface
     */
    public function addField(FieldInterface $field);

    /**
     * Removes a field from the entity.
     *
     * @param FieldInterface $field
     */
    public function removeField(FieldInterface $field);

    /**
     * Gets all the entity fields.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFields();

    /**
     * Gets all the entity field names excluding primary keys.
     *
     * This method must return all the editable field names of an entity.
     *
     * @return array
     */
    public function getFieldNamesWithoutPK();

    /**
     * Gets an entity field by name.
     *
     * @param string $name The field name.
     *
     * @return Field|false
     */
    public function getField($name);

    /**
     * Returns whether this entity owns a field by name.
     *
     * @param string $name The field name.
     *
     * @return bool
     */
    public function hasField($name);

    /**
     * Adds an association to the entity.
     *
     * @param AssociationInterface $association
     *
     * @return Entity
     */
    public function addAssociation(AssociationInterface $association);

    /**
     * Removes an association from the entity.
     *
     * @param AssociationInterface $association
     */
    public function removeAssociation(AssociationInterface $association);

    /**
     * Gets all the entity associations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociations();

    /**
     * Gets all the association names.
     *
     * @return array
     */
    public function getAssociationNames();

    /**
     * Gets an entity association by name.
     *
     * @param string $name The association name.
     *
     * @return Association|false
     */
    public function getAssociation($name);

    /**
     * Returns whether this entity owns an association by name.
     *
     * @param string $name The association name.
     *
     * @return bool
     */
    public function hasAssociation($name);

    /**
     * Gets the entity namespace.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => AppBundle\Entity
     *     2. AppBundle\Entity\Blog\Post => AppBundle\Entity\Blog
     *     3. App\BlogBundle\Entity\Post => App\BlogBundle\Entity
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Gets the entity short name.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => Post
     *     2. AppBundle\Entity\Blog\Post => Post
     *     3. App\BlogBundle\Entity\Post => Post
     *
     * @return string
     */
    public function getShortName();

    /**
     * Gets the entity simple name.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => Post
     *     2. AppBundle\Entity\Blog\Post => Blog\Post
     *     3. App\BlogBundle\Entity\Post => Post
     *
     * @return string
     */
    public function getSimpleName();

    /**
     * Gets the entity shortcut notation.
     *
     * A shortcut notation is : <bundleName>:<simpleName>.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => AppBundle:Post
     *     2. AppBundle\Entity\Blog\Post => AppBundle:Blog\Post
     *     3. App\BlogBundle\Entity\Post => AppBlogBundle:Post
     *
     * @return string
     */
    public function getShortcut();

    /**
     * Gets the entity translation key.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => post
     *     2. AppBundle\Entity\Blog\Post => blog.post
     *     3. App\BlogBundle\Entity\Post => post
     *
     * @return string
     */
    public function getTranslationKey();

    /**
     * Gets the entity route prefix.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => post
     *     2. AppBundle\Entity\Blog\Post => blog_post
     *     3. App\BlogBundle\Entity\Post => post
     *
     * @return string
     */
    public function getRoutePrefix();

    /**
     * Gets the entity directory.
     *
     * Examples:
     *     1. AppBundle\Entity\Post => null
     *     2. AppBundle\Entity\Blog\Post => Blog
     *     3. App\BlogBundle\Entity\Post => null
     *
     * @return string
     */
    public function getDirectory();

    /**
     * Returns the absolute path of the entity file.
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the absolute path of the entity mapping configuration file.
     *
     * @param string $format The configuration format.
     *
     * @return string
     */
    public function getConfigurationPath($format);
}

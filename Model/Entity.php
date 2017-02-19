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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Represents a Doctrine entity inside a Symfony application.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class Entity implements EntityInterface
{
    /**
     * Contains the entity name.
     *
     * @var string
     */
    private $name;

    /**
     * Contains the owning bundle.
     *
     * @var BundleInterface
     */
    private $bundle;

    /**
     * Contains all the entity fields.
     *
     * @var Field[]
     */
    private $fields;

    /**
     * Contains all the entity associations.
     *
     * @var Association[]
     */
    private $associations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->associations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setBundle(BundleInterface $bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function addField(FieldInterface $field)
    {
        $field->setEntity($this);

        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeField(FieldInterface $field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldNamesWithoutPK()
    {
        $fieldNames = [];

        foreach ($this->fields as $field) {
            if (!$field instanceof PrimaryKeyInterface) {
                $fieldNames[] = $field->getName();
            }
        }

        return $fieldNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        return $this
            ->fields
            ->filter(function (FieldInterface $field) use ($name) {
                return $name === $field->getName();
            })
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        return false !== $this->getField($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociation(AssociationInterface $association)
    {
        $association->setEntity($this);

        if (!$this->associations->contains($association)) {
            $this->associations->add($association);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociation(AssociationInterface $association)
    {
        $this->associations->removeElement($association);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociationNames()
    {
        $associationNames = [];

        foreach ($this->associations as $association) {
            $associationNames[] = $association->getName();
        }

        return $associationNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociation($name)
    {
        return $this
            ->associations
            ->filter(function (AssociationInterface $association) use ($name) {
                return $name === $association->getName();
            })
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation($name)
    {
        return false !== $this->getAssociation($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        $namespace = explode('\\', $this->name);
        array_pop($namespace);

        return implode('\\', $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortName()
    {
        $name = explode('\\', $this->name);

        return array_pop($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSimpleName()
    {
        return preg_replace('/.+\\\\Entity\\\\/', '', $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortcut()
    {
        return $this->bundle->getName().':'.$this->getSimpleName();
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationKey()
    {
        return strtolower(str_replace('\\', '.', $this->getSimpleName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePrefix()
    {
        return strtolower(str_replace('\\', '_', $this->getSimpleName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory()
    {
        $directory = explode('\\', $this->getSimpleName());
        array_pop($directory);

        return implode('\\', $directory);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return str_replace('\\', '/', sprintf('%s.php', str_replace(
            $this->bundle->getNamespace(),
            $this->bundle->getPath(),
            $this->getName()
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationPath($format)
    {
        if ('annotation' === $format) {
            return null;
        }

        return sprintf(
            '%s/Resources/config/doctrine/%s.orm.%s',
            $this->bundle->getPath(),
            str_replace('\\', '.', $this->getSimpleName()),
            $format
        );
    }
}

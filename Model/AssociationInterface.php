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

/**
 * Contract for an entity association model class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface AssociationInterface
{
    /**
     * Instantiate an AssociationInterface that can aggregate with a given mapping.
     *
     * @param array $mapping The association mapping informations.
     *                       [
     *                       'fieldName'    => The association name.
     *                       'type'         => The association type.
     *                       'targetEntity' => The association target entity.
     *                       'mappedBy'     => The association mappedBy property.
     *                       'inversedBy'   => The association inversedBy property.
     *                       ]
     */
    public function __construct(array $mapping = null);

    /**
     * Gets the string representation of the association.
     *
     * @return string
     */
    public function __toString();

    /**
     * Sets the association name.
     *
     * @param string $name The association name.
     *
     * @return Association
     */
    public function setName($name);

    /**
     * Gets the association name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the association type.
     *
     * @param string $type The association type.
     *
     * @return Association
     */
    public function setType($type);

    /**
     * Gets the association type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns an indexed array of all supported association type aliases.
     *
     * @return array
     */
    public function getSupportedTypes();

    /**
     * Returns the Doctrine type of an association alias.
     *
     * Handle the case: One-To-Many, Unidirectional with Join Table:
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
     *
     * @return int
     */
    public function getDoctrineType();

    /**
     * Sets the association target entity fully qualified class name.
     *
     * @param string $targetEntity The association target entity.
     *
     * @return Association
     */
    public function setTargetEntity($targetEntity);

    /**
     * Gets the association target entity fully qualified class name.
     *
     * @return string
     */
    public function getTargetEntity();

    /**
     * Sets whether the association is bidirectional.
     *
     * @param bool $bidirectional Whether the association is bidirectional.
     *
     * @return Association
     */
    public function setBidirectional($bidirectional);

    /**
     * Gets whether the association is bidirectional.
     *
     * @return bool
     */
    public function isBidirectional();

    /**
     * Sets whether the association is on the owning side of the relation.
     *
     * @param bool $owningSide Whether the association is on the owning side.
     *
     * @return Association
     */
    public function setOwningSide($owningSide);

    /**
     * Gets whether the association is on the owning side of the relation.
     *
     * @return bool
     */
    public function isOwningSide();

    /**
     * Sets the association mappedBy property.
     *
     * @param string $mappedBy The mappedBy property of the association.
     *
     * @return Association
     */
    public function setMappedBy($mappedBy);

    /**
     * Gets the association mappedBy property.
     *
     * @return string
     */
    public function getMappedBy();

    /**
     * Sets the association inversedBy property.
     *
     * @param string $inversedBy THe inversedBy property of the association.
     *
     * @return Association
     */
    public function setInversedBy($inversedBy);

    /**
     * Gets the association inversedBy property.
     *
     * @return string
     */
    public function getInversedBy();

    /**
     * Sets the owning entity.
     *
     * @param EntityInterface $entity The owning entity of the association.
     *
     * @return AssociationInterface
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Gets the owning entity.
     *
     * @return EntityInterface
     */
    public function getEntity();
}

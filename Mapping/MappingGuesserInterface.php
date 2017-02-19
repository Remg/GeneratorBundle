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

use Remg\GeneratorBundle\Model\AssociationInterface;
use Remg\GeneratorBundle\Model\FieldInterface;

/**
 * Contract for a mapping guesser class to implement.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
interface MappingGuesserInterface
{
    /**
     * Returns the best field type guess for a given FieldInterface.
     *
     * @param FieldInterface $field The field.
     *
     * @return string
     */
    public function guessFieldType(FieldInterface $field);

    /**
     * Returns the best association type guess for a given AssociationInterface.
     *
     * @param AssociationInterface $association The association.
     *
     * @return string
     */
    public function guessAssociationType(AssociationInterface $association);

    /**
     * Returns the best association target entity guess for a given
     * AssociationInterface.
     *
     * @param AssociationInterface $association The association.
     *
     * @return string
     */
    public function guessTargetEntity(AssociationInterface $association);

    /**
     * Returns the best guess about whether a given AssociationInterface is
     * bidirectional.
     *
     * @return bool
     */
    public function guessIfBidirectional();

    /**
     * Returns the best guess about whether a given AssociationInterface is
     * on the owning side of the relation.
     *
     * @return bool
     */
    public function guessIfOwningSide();

    /**
     * Returns the best inverse property guess for a given AssociationInterface.
     *
     * @param AssociationInterface $association The association.
     *
     * @return string
     */
    public function guessInverseProperty(AssociationInterface $association);
}

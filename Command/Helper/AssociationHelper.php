<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Command\Helper;

use Remg\GeneratorBundle\Model\Association;
use Remg\GeneratorBundle\Model\AssociationInterface;

/**
 * A helper to interact with the user to define an AssociationInterface.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class AssociationHelper extends MappingHelper implements AssociationHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function askAssociation(AssociationInterface $association)
    {
        $association
            ->setName($this->askName($association))
            ->setType($this->askType($association))
            ->setTargetEntity($this->askTargetEntity($association))
            ->setBidirectional($this->askIfBidirectional($association))
            ->setOwningSide($this->askIfOwningSide($association))
            ->setMappedBy($this->askMappedBy($association))
            ->setInversedBy($this->askInversedBy($association));

        return $association;
    }

    /**
     * Asks an association name.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return string The answered name.
     */
    private function askName(AssociationInterface $association)
    {
        $validator = $this->validator;

        return $this->display->ask(
            'Association name',
            $association->getName(),
            function ($answer) use ($validator, $association) {
                if ($association->getName() && $answer === $association->getName()) {
                    return $answer;
                }

                return $validator->validateFieldName($association->getEntity(), $answer);
            }
        );
    }

    /**
     * Asks an association type.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return string The answered type.
     */
    private function askType(AssociationInterface $association)
    {
        return $this->display->choice(
            'Association type',
            $association->getSupportedTypes(),
            $this->guesser->guessAssociationType($association)
        );
    }

    /**
     * Asks what is the target association of an association.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return string The answered target entity.
     */
    private function askTargetEntity(AssociationInterface $association)
    {
        return $this->display->ask(
            'Target entity',
            $this->guesser->guessTargetEntity($association),
            [$this->validator, 'validateTargetEntity']
        );
    }

    /**
     * Asks whether an association is bidirectional.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return bool Whether the association is bidirectonal.
     */
    private function askIfBidirectional(AssociationInterface $association)
    {
        return $this->display->confirm(
            'Bidirectional ?',
            $this->guesser->guessIfBidirectional($association)
        );
    }

    /**
     * Asks whether an association is on the owning side of the relation.
     *
     * This question can be answered with logic in some cases.
     * Such cases are:
     *     1. If the association is unidirectional.
     *     2. If the association type is OneToMany (always false).
     *     3. If the association type is ManyToOne (always true).
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return bool Whether the association is on the owning side of the relation.
     */
    private function askIfOwningSide(AssociationInterface $association)
    {
        if (false === $association->isBidirectional()) {
            return;
        } elseif (Association::ONE_TO_MANY === $association->getType()) {
            return false;
        } elseif (Association::MANY_TO_ONE === $association->getType()) {
            return true;
        }

        return $this->display->confirm(
            'Owning side ?',
            $this->guesser->guessIfOwningSide($association)
        );
    }

    /**
     * Asks an association inversedBy property.
     *
     * This question can be answered with logic in some cases.
     * Such cases are:
     *     1. If the association is unidirectional.
     *     2. If the association is on the inversed side of the relation.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return string The answered inversedBy property.
     */
    private function askInversedBy(AssociationInterface $association)
    {
        if (!$association->isBidirectional() || !$association->isOwningSide()) {
            return;
        }

        $validator = $this->validator;

        return $this->display->ask(
            'Inversed by :',
            $this->guesser->guessInverseProperty($association),
            function ($answer) use ($validator, $association) {
                return $validator->validateFieldName($association->getEntity(), $answer);
            }
        );
    }

    /**
     * Asks an association mappedBy property.
     *
     * This question can be answered with logic in some cases.
     * Such cases are:
     *     1. If the association is unidirectional.
     *     2. If the association is on the owning side of the relation.
     *
     * @param AssociationInterface $association The association being defined.
     *
     * @return string The answered mappedBy property.
     */
    private function askMappedBy(AssociationInterface $association)
    {
        if (!$association->isBidirectional() || $association->isOwningSide()) {
            return;
        }

        $validator = $this->validator;

        return $this->display->ask(
            'Mapped by :',
            $this->guesser->guessInverseProperty($association),
            function ($answer) use ($validator, $association) {
                return $validator->validateFieldName($association->getEntity(), $answer);
            }
        );
    }
}

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

use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Types\Type;
use Remg\GeneratorBundle\Model\Association;
use Remg\GeneratorBundle\Model\AssociationInterface;
use Remg\GeneratorBundle\Model\FieldInterface;

/**
 * This class is used to guess mapping informations to help user's input.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class MappingGuesser implements MappingGuesserInterface
{
    const GUESS_TYPE_DATE = '/^.+(d|D)ate$/';
    const GUESS_TYPE_DATETIME = '/^.+(At|(d|D)ate(time)?)$/';
    const GUESS_TYPE_TIME = '/(t|T)ime$/';
    const GUESS_TYPE_BOOLEAN = '/^(((is|has).+)|(.+ed))$/';

    /**
     * Contains an Inflector instance.
     *
     * @var Inflector
     */
    private $inflector;

    /**
     * Returns a map of the field type guess patterns referring their types.
     *
     * @return array
     */
    private function getFieldTypeGuessesMap()
    {
        return [
            static::GUESS_TYPE_DATE     => Type::DATE,
            static::GUESS_TYPE_DATETIME => Type::DATETIME,
            static::GUESS_TYPE_TIME     => Type::TIME,
            static::GUESS_TYPE_BOOLEAN  => Type::BOOLEAN,
        ];
    }

    /**
     * Returns an Inflector instance.
     *
     * @return Inflector
     */
    private function getInflector()
    {
        if (!$this->inflector) {
            $this->inflector = new Inflector();
        }

        return $this->inflector;
    }

    /**
     * {@inheritdoc}
     */
    public function guessFieldType(FieldInterface $field)
    {
        if (null !== $type = $field->getType()) {
            return $type;
        }

        foreach ($this->getFieldTypeGuessesMap() as $pattern => $guess) {
            if (preg_match($pattern, $field->getName())) {
                return $guess;
            }
        }

        // default answer to 'string'
        return Type::STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function guessAssociationType(AssociationInterface $association)
    {
        if (null !== $type = $association->getType()) {
            return $type;
        }

        $name = $association->getName();

        // If the association field name looks like a plural word,
        // we assume the association is a collection valued relation.
        if ($this->getInflector()->pluralize($name) === $name) {
            // Default choice to most common TO_MANY association (OneToMany)
            return Association::ONE_TO_MANY;
        }

        // Then we assume that the asociation is a single valued relation.
        // Default choice to most common TO_ONE association (ManyToOne)
        return Association::MANY_TO_ONE;
    }

    /**
     * {@inheritdoc}
     */
    public function guessTargetEntity(AssociationInterface $association)
    {
        if (null !== $targetEntity = $association->getTargetEntity()) {
            return $targetEntity;
        }

        $bundle = $association->getEntity()->getBundle();

        $shortName = $association->getName();
        $shortName = $this->getInflector()->singularize($shortName);
        $shortName = ucfirst($shortName);

        return sprintf('%s:%s', $bundle->getName(), $shortName);
    }

    /**
     * {@inheritdoc}
     */
    public function guessIfBidirectional()
    {
        // todo
        // maybe inspect the target entity by injecting the
        // \Remg\GeneratorBundle\Mapping\EntityFactory
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function guessIfOwningSide()
    {
        // todo
        // maybe inspect the target entity by injecting the
        // \Remg\GeneratorBundle\Mapping\EntityFactory
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function guessInverseProperty(AssociationInterface $association)
    {
        if (null !== $mappedBy = $association->getMappedBy()) {
            return $mappedBy;
        }

        if (null !== $inversedBy = $association->getInversedBy()) {
            return $inversedBy;
        }

        $property = $association->getEntity()->getShortName();

        if (
            Association::MANY_TO_ONE === $association->getType() ||
            Association::MANY_TO_MANY === $association->getType()
        ) {
            $property = $this->getInflector()->pluralize($property);
        }

        return lcfirst($property);
    }
}

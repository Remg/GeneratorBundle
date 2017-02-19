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

use Doctrine\DBAL\Types\Type;
use Remg\GeneratorBundle\Model\FieldInterface;

/**
 * A helper to interact with the user to define a FieldInterface.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class FieldHelper extends MappingHelper implements FieldHelperInterface
{
    /**
     * Default values for interactions.
     */
    const DEFAULT_LENGTH = 255;
    const DEFAULT_PRECISION = 10;
    const DEFAULT_SCALE = 2;
    const DEFAULT_NULLABLE = false;
    const DEFAULT_UNIQUE = false;

    /**
     * {@inheritdoc}
     */
    public function askField(FieldInterface $field)
    {
        $field
            ->setName($this->askName($field))
            ->setType($this->askType($field))
            ->setLength($this->askLength($field))
            ->setPrecision($this->askPrecision($field))
            ->setScale($this->askScale($field))
            ->setNullable($this->askIfNullable($field))
            ->setUnique($this->askIfUnique($field));

        return $field;
    }

    /**
     * Asks a field name.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return string The answered name.
     */
    private function askName(FieldInterface $field)
    {
        $validator = $this->validator;

        return $this->display->ask(
            'Field name',
            $field->getName(),
            function ($answer) use ($validator, $field) {
                if ($field->getName() && $answer === $field->getName()) {
                    return $answer;
                }

                return $validator->validateFieldName($field->getEntity(), $answer);
            }
        );
    }

    /**
     * Asks a field type.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return string The answered type.
     */
    private function askType(FieldInterface $field)
    {
        return $this->display->choice(
            'Field type',
            $field->getSupportedTypes(),
            $this->guesser->guessFieldType($field)
        );
    }

    /**
     * Asks a field length.
     *
     * This only applies for Type::STRING fields.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return int The answered length.
     */
    private function askLength(FieldInterface $field)
    {
        if (Type::STRING !== $field->getType()) {
            return;
        }

        return $this->display->ask(
            'Field length',
            $field->getLength() ?: static::DEFAULT_LENGTH,
            [$this->validator, 'validateFieldLength']
        );
    }

    /**
     * Asks a field precision.
     *
     * This only applies for Type::DECIMAL fields.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return int The answered precision.
     */
    private function askPrecision(FieldInterface $field)
    {
        if (Type::DECIMAL !== $field->getType()) {
            return;
        }

        return $this->display->ask(
            'Field precision',
            $field->getPrecision() ?: static::DEFAULT_PRECISION,
            [$this->validator, 'validateFieldPrecision']
        );
    }

    /**
     * Asks a field scale.
     *
     * This only applies for Type::DECIMAL fields.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return int The answered scale.
     */
    private function askScale(FieldInterface $field)
    {
        if (Type::DECIMAL !== $field->getType()) {
            return;
        }

        $validator = $this->validator;

        return $this->display->ask(
            'Field scale',
            $field->getScale() ?: static::DEFAULT_SCALE,
            function ($answer) use ($validator, $field) {
                return $validator->validateFieldScale($answer, $field->getPrecision());
            }
        );
    }

    /**
     * Asks whether a field is nullable.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return bool Whether the field is nullable.
     */
    private function askIfNullable(FieldInterface $field)
    {
        return $this->display->confirm(
            'Nullable ?',
            $field->isNullable() ?: static::DEFAULT_NULLABLE
        );
    }

    /**
     * Asks whether a field has a unique constraint.
     *
     * @param FieldInterface $field The entity field being defined.
     *
     * @return bool Whether the field has a unique constraint.
     */
    private function askIfUnique(FieldInterface $field)
    {
        return $this->display->confirm(
            'Unique ?',
            $field->isUnique() ?: static::DEFAULT_UNIQUE
        );
    }
}

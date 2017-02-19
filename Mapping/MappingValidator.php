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
use Remg\GeneratorBundle\Exception\MappingException;
use Remg\GeneratorBundle\Model\EntityInterface;

/**
 * This class is used to validate mapping informations of the user input.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class MappingValidator implements MappingValidatorInterface
{
    /**
     * Simple entity shortcut pattern to detect entity shortcut notation
     * and retrieve bundle name and entity simple name parts.
     *
     * @var string
     */
    const ENTITY_SHORTCUT = '{^(?<bundle>.+):(?<simpleName>.+)$}';

    /**
     * Simple entity fully qualified class name pattern to detect entity fqcn
     * and retrieve bundle name and entity simple name parts.
     *
     * @var string
     */
    const ENTITY_FQCN = '{^(?<bundle>.+)\\\\Entity\\\\(?<simpleName>.+)$}';

    /**
     * Simple entity notation, that combine both shortcut notation and fully
     * qualified class name, to retrieve bundle name and entity simple name parts.
     *
     * @var string
     */
    const ENTITY_NAME = '{^(?<bundle>.+)(?<separator>:|\\\\Entity\\\\)(?<simpleName>.+)$}';

    /**
     * Pattern to validate that a given string is a valid PHP variable name.
     *
     * @var string
     */
    const PHP_VALID_VAR_NAME = '{^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$}';

    const MINIMUM_FIELD_LENGTH = 1;
    const MAXIMUM_FIELD_LENGTH = null;
    const MINIMUM_FIELD_PRECISION = 1;
    const MAXIMUM_FIELD_PRECISION = 65;
    const MINIMUM_FIELD_SCALE = 1;
    const MAXIMUM_FIELD_SCALE = 30;

    /**
     * Contains an EntityFactoryInterface instance.
     *
     * @var EntityFactoryInterface
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param EntityFactoryInterface $factory
     */
    public function __construct(EntityFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function validateNewName($name)
    {
        $name = $this->normalizeName($name);

        if ($this->factory->hasEntity($name)) {
            $entity = $this->factory->getEntity($name);

            throw new MappingException(<<<EOT
The entity "{$entity->getName()}" already exists.
(Located in {$entity->getPath()})
EOT
            );
        }

        $this->assertNoKeywords($name);

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFieldName(EntityInterface $entity, $name)
    {
        if (empty($name)) {
            throw new MappingException('A field or association name can not be empty.');
        }

        $this->assertNotMapped($entity, $name);
        $this->assertValidPhpVariable($name);
        $this->assertNoKeywords($name);

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFieldLength($length)
    {
        $min = static::MINIMUM_FIELD_LENGTH;
        $max = static::MAXIMUM_FIELD_LENGTH;

        $this->assertValidInteger($length, $min, $max);

        return $length;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFieldPrecision($precision)
    {
        $min = static::MINIMUM_FIELD_PRECISION;
        $max = static::MAXIMUM_FIELD_PRECISION;

        $this->assertValidInteger($precision, $min, $max);

        return $precision;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFieldScale($scale, $precision)
    {
        // Do not set the given precision as a maximum value here, this
        // check will be made right after to display a more specific error
        // message to the user, explaining the scale can not be greater than
        // the precision.
        $min = static::MINIMUM_FIELD_SCALE;
        $max = static::MAXIMUM_FIELD_SCALE;

        $this->assertValidInteger($scale, $min, $max);

        if ($scale >= $precision) {
            throw new MappingException(sprintf(
                'The field scale can\'t be greater or equal than the field precision. (%s >= %s)',
                $scale,
                $precision
            ));
        }

        return $scale;
    }

    /**
     * {@inheritdoc}
     */
    public function validateTargetEntity($name)
    {
        return $this->normalizeName($name);
    }

    /**
     * Normalized an entity name to a fully qualified class name.
     *
     * @param string $name The entity name to normalize.
     *
     * @throws BundleNotFoundException
     *
     * @return string The entity fully qualified class name.
     */
    protected function normalizeName($name)
    {
        // Replace slashes "/" with backslashes "\" for easier typing.
        $name = trim(strtr($name, '/', '\\'));

        if (!preg_match(static::ENTITY_NAME, $name, $parts)) {
            throw new MappingException(<<<EOT
The entity name "$name" is not valid.

Supported notations are:
  1. Fully qualified class name (e.g., AppBundle\Entity\Post).
  2. Shortcut notation (e.g., AppBundle:Post).

You can replace "\" with "/" for easier typing.
EOT
            );
        }

        $this->assertValidBundle($parts['bundle']);

        if (preg_match(static::ENTITY_SHORTCUT, $name)) {
            $name = $this->factory->getFqcnFromShortcut($name);
        }

        // Uppercase words.
        $inflector = new Inflector();
        $name = $inflector->ucwords($name, '\\');

        return $name;
    }

    /**
     * Checks that a given name is a known bundle name.
     *
     * This method asserts that:
     *     1. The bundle name is known and enabled.
     *
     * @param string $name The bundle name or namespace.
     *
     * @throws Remg\GeneratorBundle\Exception\BundleNotFoundException
     */
    protected function assertValidBundle($name)
    {
        $this->factory->getBundle($name);
    }

    /**
     * Validates that a given variable is a valid integer.
     *
     * This method asserts that:
     *   1. The variable $integer is a valid integer.
     *   2. If $min is set, $integer must be greater or equal than $min.
     *   3. If $max is set, $integer must be lower or equal than $max.
     *
     * @param int $integer The variable to validate
     * @param int $min     The minimum $integer should be
     * @param int $max     The maximum $integer should be
     *
     * @throws FieldMappingException If one of the assertion fails.
     */
    protected function assertValidInteger($integer, $min = null, $max = null)
    {
        if (false === filter_var($integer, FILTER_VALIDATE_INT)) {
            throw new MappingException(sprintf(
                '"%s" is not a valid integer value.', $integer
            ));
        }

        if (null !== $min && $integer < $min) {
            throw new MappingException(sprintf(
                'You must specify a value greater or equal than "%s" ("%s" given).',
                $min,
                $integer
            ));
        }

        if (null !== $max && $integer > $max) {
            throw new MappingException(sprintf(
                'You must specify a value lower or equal than "%s" ("%s" given).',
                $max,
                $integer
            ));
        }
    }

    /**
     * Checks that a given variable name is a valid PHP variable name.
     *
     * This method asserts that:
     *     1. The name contains only valid characters.
     *
     * @param string $name THe name to inspect.
     *
     * @throws MappingException If the name is not valid.
     */
    protected function assertValidPhpVariable($name)
    {
        if (!preg_match(static::PHP_VALID_VAR_NAME, $name)) {
            throw new MappingException(sprintf(
                '"%s" is not a valid PHP variable name.', $name
            ));
        }
    }

    /**
     * Checks that a given string has no PHP or platform related keyword.
     *
     * This method asserts that:
     *     1. The string does not contain a PHP keyword.
     *     2. The string does not contain a platform keyword.
     *
     * @param string $string The string to inspect.
     *
     * @throws MappingException If a keyword is detected.
     */
    protected function assertNoKeywords($string)
    {
        $words = explode('\\', $string);

        foreach ($words as $word) {
            if (in_array(strtolower($word), $this->getPhpKeywords())) {
                throw new MappingException(sprintf(
                    '"%s" is a reserved PHP keyword.',
                    strtolower($word)
                ));
            }

            if ($this->getPlatformKeywordList()->isKeyword($word)) {
                throw new MappingException(sprintf(
                    '"%s" is a reserved %s keyword.',
                    $word,
                    $this->getPlatformKeywordList()->getName()
                ));
            }
        }
    }

    /**
     * Checks that a given entity field/association name is not already mapped.
     *
     * This method asserts that:
     *   1. The entity does not own a field with the same name.
     *   2. The entity does not own an association with the same name.
     *
     * @param EntityInterface $entity The entity being validated.
     * @param string          $name   The field/association name being validated.
     *
     * @throws FieldMappingException If one of the assertion fails.
     */
    protected function assertNotMapped(EntityInterface $entity, $name)
    {
        if ($entity->hasField($name)) {
            throw new MappingException(sprintf(
                'The entity "%s" already has a field named "%s".',
                $entity->getName(),
                $name
            ));
        }

        if ($entity->hasAssociation($name)) {
            throw new MappingException(sprintf(
                'The entity "%s" already has an association named "%s".',
                $entity->getName(),
                $name
            ));
        }
    }

    /**
     * Returns the current platform keyword list instance.
     *
     * @return \Doctrine\DBAL\Platforms\Keywords\KeywordList
     */
    protected function getPlatformKeywordList()
    {
        return $this->factory->getPlatformKeywordList();
    }

    /**
     * Returns a list of all php keywords.
     *
     * @return array
     */
    protected function getPhpKeywords()
    {
        return [
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'do',
            'else',
            'elseif',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'interface',
            'instanceof',
            'insteadof',
            'namespace',
            'new',
            'or',
            'private',
            'protected',
            'public',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'use',
            'var',
            'while',
            'xor',
            'yield',
            '__class__',
            '__dir__',
            '__file__',
            '__line__',
            '__function__',
            '__method__',
            '__namespace__',
            '__trait__',
            '__halt_compiler',
            'die',
            'echo',
            'empty',
            'exit',
            'eval',
            'include',
            'include_once',
            'isset',
            'list',
            'require',
            'require_once',
            'return',
            'print',
            'unset',
        ];
    }
}

<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Mapping\MappingValidator;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the MappingValidator class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class MappingValidatorTest extends TestCase
{
    use Fixtures\Mock\AssociationMock,
        Fixtures\Mock\BundleNotFoundExceptionMock,
        Fixtures\Mock\EntityFactoryMock,
        Fixtures\Mock\EntityMock,
        Fixtures\Mock\FieldMock,
        Fixtures\Provider\AssociationProvider,
        Fixtures\Provider\EntityProvider,
        Fixtures\Provider\FieldProvider;

    /**
     * Tests the method "validateNewName()".
     *
     * @dataProvider entityInputProvider
     */
    public function testValidateNewName($input, $fqcn, $normalized)
    {
        $factory = $this->getEntityFactory();
        $factory->method('getFqcnFromShortcut')->willReturn($fqcn);

        $validator = new MappingValidator($factory);

        $name = $validator->validateNewName($input);

        $this->assertEquals($normalized, $name, 'The normalized name is wrong.');
    }

    /**
     * Tests the method "validateNewName()" when the entity already exists.
     *
     * @dataProvider entityProvider
     * @expectedException Remg\GeneratorBundle\Exception\MappingException
     */
    public function testValidateNewNameWithExistingEntity(array $mapping)
    {
        $factory = $this->getEntityFactory();
        $factory->method('hasEntity')->willReturn(true);
        $factory->method('getEntity')->willReturn(
            $this->getEntity($mapping['name'])
        );

        $validator = new MappingValidator($factory);

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('The entity "%s" already exists.', $mapping['name']));

        $validator->validateNewName($mapping['name']);
    }

    /**
     * Tests the method "validateNewName()" when the bundle is not known.
     *
     * @dataProvider entityProvider
     * @expectedException Remg\GeneratorBundle\Exception\BundleNotFoundException
     */
    public function testValidateNewNameWithNoBundle(array $mapping)
    {
        $factory = $this->getEntityFactory();

        $exception = $this->getBundleNotFoundException();
        $factory->method('getBundle')->will($this->throwException($exception));

        $validator = new MappingValidator($factory);

        $validator->validateNewName($mapping['name']);
    }

    /**
     * Tests the method "validateNewName()" when the name has a php keyword.
     *
     * @dataProvider entityPhpKeywordProvider
     */
    public function testValidateNewNameWithPhpKeyword($input, $keyword)
    {
        $factory = $this->getEntityFactory();

        $validator = new MappingValidator($factory);

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is a reserved PHP keyword.', $keyword));

        $validator->validateNewName($input);
    }

    /**
     * Tests the method "validateNewName()" when the name has a php keyword.
     *
     * @dataProvider entityPlatformKeywordProvider
     */
    public function testValidateNewNameWithPlatformKeyword($input, $keyword, $platform)
    {
        $factory = $this->getEntityFactory();
        $factory
            ->getPlatformKeywordList()
            ->method('isKeyword')
            ->will($this->returnValueMap([[$keyword, true]]));

        $factory
            ->getPlatformKeywordList()
            ->method('getName')
            ->willReturn($platform);

        $validator = new MappingValidator($factory);

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is a reserved %s keyword.', $keyword, $platform));

        $validator->validateNewName($input);
    }

    /**
     * Tests the method "validateNewName()" when the given name is invalid.
     *
     * @dataProvider entityInvalidNameProvider
     */
    public function testValidateNewNameWithInvalidName($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('The entity name "%s" is not valid.', $input));

        $validator->validateNewName($input);
    }

    /**
     * Tests the method "validateFieldName()".
     *
     * @dataProvider fieldProvider
     */
    public function testValidateFieldName(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post');

        $validator = new MappingValidator($this->getEntityFactory());

        $fieldName = $validator->validateFieldName($entity, $mapping['fieldName']);

        $this->assertEquals($mapping['fieldName'], $fieldName, 'The resolved field name is wrong.');
    }

    /**
     * Tests the method "validateFieldName()" when the field name is empty.
     */
    public function testValidateFieldNameEmpty()
    {
        $entity = $this->getEntity('AppBundle\Entity\Post');

        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage('A field or association name can not be empty.');

        $validator->validateFieldName($entity, null);
    }

    /**
     * Tests the method "validateFieldName()" when the owning Entity
     * already has a field with the same name.
     *
     * @dataProvider fieldProvider
     */
    public function testValidateFieldNameFieldExists(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post', [
            $mapping['fieldName'] => $this->getField($mapping),
        ]);

        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'The entity "%s" already has a field named "%s".',
            'AppBundle\Entity\Post',
            $mapping['fieldName']
        ));

        $validator->validateFieldName($entity, $mapping['fieldName']);
    }

    /**
     * Tests the method "validateFieldName()" when the owning Entity
     * already has an association with the same name.
     *
     * @dataProvider associationProvider
     */
    public function testValidateFieldNameAssociationExists(array $mapping)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post', [], [
            $mapping['fieldName'] => $this->getAssociation($mapping),
        ]);

        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'The entity "%s" already has an association named "%s".',
            'AppBundle\Entity\Post',
            $mapping['fieldName']
        ));

        $validator->validateFieldName($entity, $mapping['fieldName']);
    }

    /**
     * Tests the method "validateFieldName()" when the given field name
     * is an invalid PHP variable name.
     *
     * @dataProvider fieldInvalidPhpProvider
     */
    public function testValidateFieldNameInvalidPhp($input)
    {
        $entity = $this->getEntity('AppBundle\Entity\Post');

        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is not a valid PHP variable name.', $input));

        $validator->validateFieldName($entity, $input);
    }

    /**
     * Tests the method "validateFieldLength()".
     *
     * @dataProvider fieldLengthProvider
     */
    public function testValidateFieldLength($length)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $fieldLength = $validator->validateFieldLength($length);
        $this->assertEquals($fieldLength, $length, 'The resolved field length is wrong.');
    }

    /**
     * Tests the method "validateFieldLength()" when the given length is invalid.
     *
     * @dataProvider invalidIntegerProvider
     */
    public function testValidateFieldLengthInvalid($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is not a valid integer value.', $input));

        $validator->validateFieldLength($input);
    }

    /**
     * Tests the method "validateFieldLength()" when the given length is negative.
     *
     * @dataProvider negativeIntegerProvider
     */
    public function testValidateFieldLengthNegative($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'You must specify a value greater or equal than "%s" ("%s" given).',
            1,
            $input
        ));

        $validator->validateFieldLength($input);
    }

    /**
     * Tests the method "validateFieldPrecision()".
     *
     * @dataProvider fieldPrecisionProvider
     */
    public function testValidateFieldPrecision($precision)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $fieldPrecision = $validator->validateFieldPrecision($precision);
        $this->assertEquals($fieldPrecision, $precision, 'The resolved field precision is wrong.');
    }

    /**
     * Tests the method "validateFieldPrecision()" when the given precision
     * is invalid.
     *
     * @dataProvider invalidIntegerProvider
     */
    public function testValidateFieldPrecisionInvalid($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is not a valid integer value.', $input));

        $validator->validateFieldPrecision($input);
    }

    /**
     * Tests the method "validateFieldPrecision()" when the given precision
     * is negative.
     *
     * @dataProvider negativeIntegerProvider
     */
    public function testValidateFieldPrecisionNegative($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'You must specify a value greater or equal than "%s" ("%s" given).',
            1,
            $input
        ));

        $validator->validateFieldPrecision($input);
    }

    /**
     * Tests the method "validateFieldPrecision()" when the given precision
     * is greater than the maximum allowed.
     *
     * @dataProvider precisionGreaterThanMaximumProvider
     */
    public function testValidateFieldPrecisionGreaterThanMaximum($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'You must specify a value lower or equal than "%s" ("%s" given).',
            65,
            $input
        ));

        $validator->validateFieldPrecision($input);
    }

    /**
     * Tests the method "validateFieldScale()".
     *
     * @dataProvider fieldScaleProvider
     */
    public function testValidateFieldScale($scale, $precision)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $fieldScale = $validator->validateFieldScale($scale, $precision);
        $this->assertEquals($fieldScale, $scale, 'The resolved field scale is wrong.');
    }

    /**
     * Tests the method "validateFieldScale()" when the given scale
     * is invalid.
     *
     * @dataProvider invalidIntegerProvider
     */
    public function testValidateFieldScaleInvalid($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf('"%s" is not a valid integer value.', $input));

        $validator->validateFieldScale($input, 65);
    }

    /**
     * Tests the method "validateFieldScale()" when the given scale
     * is negative.
     *
     * @dataProvider negativeIntegerProvider
     */
    public function testValidateFieldScaleNegative($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'You must specify a value greater or equal than "%s" ("%s" given).',
            1,
            $input
        ));

        $validator->validateFieldScale($input, 65);
    }

    /**
     * Tests the method "validateFieldScale()" when the given scale
     * is greater than the maximum allowed.
     *
     * @dataProvider scaleGreaterThanMaximumProvider
     */
    public function testValidateFieldScaleGreaterThanMaximum($input)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'You must specify a value lower or equal than "%s" ("%s" given).',
            30,
            $input
        ));

        $validator->validateFieldScale($input, 65);
    }

    /**
     * Tests the method "validateFieldScale()" when the given scale
     * is greater than the given precision.
     *
     * @dataProvider scaleGreaterThanPrecisionProvider
     */
    public function testValidateFieldScaleGreaterThanPrecision($input, $precision)
    {
        $validator = new MappingValidator($this->getEntityFactory());

        $this->expectException('Remg\GeneratorBundle\Exception\MappingException');
        $this->expectExceptionMessage(sprintf(
            'The field scale can\'t be greater or equal than the field precision. (%s >= %s)',
            $input,
            $precision
        ));

        $validator->validateFieldScale($input, $precision);
    }

    /**
     * Tests the method "validateTargetEntity()".
     *
     * @dataProvider entityInputProvider
     */
    public function testValidateTargetEntity($input, $fqcn, $normalized)
    {
        $factory = $this->getEntityFactory();
        $factory->method('getFqcnFromShortcut')->willReturn($fqcn);

        $validator = new MappingValidator($factory);

        $targetEntity = $validator->validateTargetEntity($input);

        $this->assertEquals($normalized, $targetEntity, 'The normalized target entity is wrong.');
    }
}

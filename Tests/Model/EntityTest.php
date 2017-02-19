<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Model\Entity;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the Entity class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityTest extends TestCase
{
    use Fixtures\Mock\AssociationMock,
        Fixtures\Mock\BundleMock,
        Fixtures\Mock\FieldMock,
        Fixtures\Mock\PrimaryKeyMock,
        Fixtures\Provider\EntityProvider;

    /**
     * Tests that the basic setters and getters.
     *
     * @dataProvider entityProvider
     */
    public function testGetSet(array $mapping)
    {
        $bundle = $this->getBundle();

        $entity = new Entity();
        $entity
            ->setName($mapping['name'])
            ->setBundle($bundle);

        $this->assertEquals($mapping['name'], $entity->getName(), 'The name should not have changed.');
        $this->assertEquals($bundle, $entity->getBundle(), 'The bundle should not have changed.');
    }

    /**
     * Tests that the Entity properly manages its field collection.
     */
    public function testFieldCollection()
    {
        $fieldName1 = 'fieldName1';
        $fieldName2 = 'fieldName2';

        // Create mocked Fields based on the given field names.
        $field1 = $this->getField(['fieldName' => $fieldName1]);
        $field2 = $this->getField(['fieldName' => $fieldName2]);

        $entity = new Entity();

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\Collection',
            $entity->getFields(),
            'The fields must be a Collection instance.'
        );
        $this->assertEmpty($entity->getFields(), 'The field collection should be empty.');
        $this->assertFalse($entity->hasField($fieldName1), 'The entity should not own this field.');
        $this->assertFalse($entity->hasField($fieldName2), 'The entity should not own this field.');

        // Map the first field to the entity.
        $entity->addField($field1);
        $this->assertCount(1, $entity->getFields(), 'The entity should now own one field.');
        $this->assertTrue($entity->hasField($fieldName1), 'The entity should now own this field.');
        $this->assertFalse($entity->hasField($fieldName2), 'The entity should not own this field.');
        $this->assertEquals($field1, $entity->getField($fieldName1), 'The expected field does not match the resolved field.');

        // Map the second field to the entity.
        $entity->addField($field2);
        $this->assertCount(2, $entity->getFields(), 'The entity should now own two fields.');
        $this->assertTrue($entity->hasField($fieldName1), 'The entity should still own this field.');
        $this->assertTrue($entity->hasField($fieldName2), 'The entity should now own this field.');
        $this->assertEquals($field1, $entity->getField($fieldName1), 'The expected field does not match the resolved field.');
        $this->assertEquals($field2, $entity->getField($fieldName2), 'The expected field does not match the resolved field.');

        // Remove the first field from the entity.
        $entity->removeField($field1);
        $this->assertCount(1, $entity->getFields(), 'The entity should now own two fields.');
        $this->assertFalse($entity->hasField($fieldName1), 'The entity should not own this field anymore.');
        $this->assertTrue($entity->hasField($fieldName2), 'The entity should still own this field.');
        $this->assertEquals($field2, $entity->getField($fieldName2), 'The expected field does not match the resolved field.');

        // Remove the second field from the entity.
        $entity->removeField($field2);
        $this->assertEmpty($entity->getFields(), 'The entity should now own one field.');
        $this->assertFalse($entity->hasField($fieldName1), 'The entity should not own this field anymore.');
        $this->assertFalse($entity->hasField($fieldName2), 'The entity should not own this field anymore.');
    }

    /**
     * Tests that the Entity properly manages its association collection.
     */
    public function testAssociationCollection()
    {
        $fieldName1 = 'fieldName1';
        $fieldName2 = 'fieldName2';

        // Create mocked Associations based on the given field names.
        $association1 = $this->getAssociation(['fieldName' => $fieldName1]);
        $association2 = $this->getAssociation(['fieldName' => $fieldName2]);

        $entity = new Entity();

        $this->assertInstanceOf(
            'Doctrine\Common\Collections\Collection',
            $entity->getAssociations(),
            'The associations must be a Collection instance.'
        );
        $this->assertEmpty($entity->getAssociations(), 'The association collection should be empty.');
        $this->assertFalse($entity->hasAssociation($fieldName1), 'The entity should not own this association.');
        $this->assertFalse($entity->hasAssociation($fieldName2), 'The entity should not own this association.');

        // Map the first association to the entity.
        $entity->addAssociation($association1);
        $this->assertCount(1, $entity->getAssociations(), 'The entity should now own one association.');
        $this->assertTrue($entity->hasAssociation($fieldName1), 'The entity should now own this association.');
        $this->assertFalse($entity->hasAssociation($fieldName2), 'The entity should not own this association.');
        $this->assertEquals($association1, $entity->getAssociation($fieldName1), 'The expected association does not match the resolved association.');

        // Map the second association to the entity.
        $entity->addAssociation($association2);
        $this->assertCount(2, $entity->getAssociations(), 'The entity should now own two associations.');
        $this->assertTrue($entity->hasAssociation($fieldName1), 'The entity should still own this association.');
        $this->assertTrue($entity->hasAssociation($fieldName2), 'The entity should now own this association.');
        $this->assertEquals($association1, $entity->getAssociation($fieldName1), 'The expected association does not match the resolved association.');
        $this->assertEquals($association2, $entity->getAssociation($fieldName2), 'The expected association does not match the resolved association.');

        // Remove the first association from the entity.
        $entity->removeAssociation($association1);
        $this->assertCount(1, $entity->getAssociations(), 'The entity should now own two associations.');
        $this->assertFalse($entity->hasAssociation($fieldName1), 'The entity should not own this association anymore.');
        $this->assertTrue($entity->hasAssociation($fieldName2), 'The entity should still own this association.');
        $this->assertEquals($association2, $entity->getAssociation($fieldName2), 'The expected association does not match the resolved association.');

        // Remove the second association from the entity.
        $entity->removeAssociation($association2);
        $this->assertEmpty($entity->getAssociations(), 'The entity should now own one association.');
        $this->assertFalse($entity->hasAssociation($fieldName1), 'The entity should not own this association anymore.');
        $this->assertFalse($entity->hasAssociation($fieldName2), 'The entity should not own this association anymore.');
    }

    /**
     * Tests that an Entity properly computes strings based on its name.
     *
     * @dataProvider entityProvider
     */
    public function testComputing(array $mapping)
    {
        $bundle = $this->getBundle(
            $mapping['bundleName'],
            $mapping['bundleNamespace'],
            $mapping['bundlePath']
        );

        $entity = new Entity();
        $entity
            ->setName($mapping['name'])
            ->setBundle($bundle);

        // Test namespace computing.
        $this->assertEquals($mapping['namespace'], $entity->getNamespace(), 'The namespace is not well computed.');
        $this->assertRegexp('/^.+\\\\Entity(.+)?$/', $entity->getNamespace(), 'The namespace must contain "\Entity".');

        // Test short name computing.
        $this->assertEquals($mapping['shortName'], $entity->getShortName(), 'The short name is not well computed.');

        // Test directory computing.
        $this->assertEquals($mapping['directory'], $entity->getDirectory(), 'The directory is not well computed.');

        // Test shortcut notation computing.
        $this->assertEquals($mapping['shortcut'], $entity->getShortcut(), 'The shortcut notation is not well computed.');
        $this->assertRegexp('/.+:.+/', $entity->getShortcut(), 'The shortcut notation separator is missing.');

        // Test translation key computing.
        $this->assertEquals($mapping['translationKey'], $entity->getTranslationKey(), 'The translation key is not well computed.');
        $this->assertRegexp('/[a-z0-9.]+/', $entity->getTranslationKey(), 'The translation key contains invalid characters.');

        // Test route prefix computing.
        $this->assertEquals($mapping['routePrefix'], $entity->getRoutePrefix(), 'The route prefix is not well computed.');
        $this->assertRegexp('/[a-z0-9_]+/', $entity->getRoutePrefix(), 'The route prefix contains invalid characters.');

        // Test path computing.
        $this->assertEquals($mapping['entityPath'], $entity->getPath(), 'The entity file path is not well computed.');
        $this->assertRegexp('/\.php$/', $entity->getPath(), 'The entity file path must end with ".php".');

        // Test path computing.
        $this->assertEquals(
            $mapping['configurationPath'],
            $entity->getConfigurationPath($mapping['format']),
            'The entity mapping configuration file path is not well computed.'
        );

        if ('annotation' !== $mapping['format']) {
            $this->assertRegexp(
                '/\.'.preg_quote($mapping['format']).'$/',
                $entity->getConfigurationPath($mapping['format']),
                sprintf('The entity mapping configuration file path must end with ".%s".', $mapping['format'])
            );
        }
    }

    /**
     * Tests the method "getFieldNamesWithoutPK()".
     */
    public function testGetFieldNamesWithoutPK()
    {
        $entity = new Entity();
        $entity
            ->addField($this->getPrimaryKey('id'))
            ->addField($this->getField(['fieldName' => 'fieldName']));

        $fieldNames = $entity->getFieldNamesWithoutPK();

        $this->assertCount(1, $fieldNames, 'There should only be one field name excluding PK.');
        $this->assertTrue(in_array('fieldName', $fieldNames), 'The field name should not be filtered.');
        $this->assertFalse(in_array('id', $fieldNames), 'The PK field name should be filtered.');
    }

    /**
     * Tests the method "getAssociationNames()".
     */
    public function testGetAssociationNames()
    {
        $entity = new Entity();
        $entity
            ->addAssociation($this->getAssociation(['fieldName' => 'name1']))
            ->addAssociation($this->getAssociation(['fieldName' => 'name2']));

        $associationNames = $entity->getAssociationNames();

        $this->assertCount(2, $associationNames, 'The should be two associaions names.');
        $this->assertTrue(in_array('name1', $associationNames), 'The association name "name1" should be present.');
        $this->assertTrue(in_array('name2', $associationNames), 'The association name "name2" shoud be present.');
    }
}

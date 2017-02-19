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
use Remg\GeneratorBundle\Model\EntityInterface;
use Remg\GeneratorBundle\Model\Field;
use Remg\GeneratorBundle\Model\PrimaryKeyInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * A helper to interact with the user to define an EntityInterface.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class EntityHelper extends MappingHelper implements EntityHelperInterface
{
    const DEFAULT_NAME = 'AppBundle:Post';
    const CHOICE_SKIP = 'None, skip this step.';

    /**
     * Contains an instance of a FieldHelperInterface.
     *
     * @var FieldHelperInterface
     */
    private $fieldHelper;

    /**
     * Contains an instance of a AssociationHelperInterface.
     *
     * @var AssociationHelperInterface
     */
    private $associationHelper;

    /**
     * {@inheritdoc}
     */
    public function askName($default = self::DEFAULT_NAME)
    {
        return $this->display->ask(
            'Entity name',
            $default,
            [$this->validator, 'validateNewName']
        );
    }

    /**
     * Asks the user to select a field name.
     *
     * @param EntityInterface[] $entities An array of editable EntityInterface.
     * @param string            $default  The default entity name to select.
     *
     * @return string The answered field name.
     */
    public function selectEntity(array $entities, $default = null)
    {
        return $this->display->choice(
            'Select the <comment>entity</comment> you want to <comment>edit</comment>',
            array_keys($entities),
            $default
        );
    }

    /**
     * {@inheritdoc}
     */
    public function askEntity(EntityInterface $entity)
    {
        $this->display->title('Building "'.$entity->getName().'"');

        if (!empty($entity->getFieldNamesWithoutPK())) {
            $this->display->section('Fields edition');
            $this->editFields($entity);
        }

        $this->display->section('Fields creation');
        $this->addFields($entity);

        if (!$entity->getAssociations()->isEmpty()) {
            $this->display->section('Associations edition');
            $this->editAssociations($entity);
        }

        $this->display->section('Associations creation');
        $this->addAssociations($entity);

        $this->summarize($entity);

        if ($this->display->confirm(
            'Do you want to <comment>edit</comment> the entity <comment>before</comment> generation ?',
            false
        )) {
            return $this->askEntity($entity);
        }
    }

    /**
     * Displays the summary of an entity.
     *
     * @param EntityInterface $entity The entity to summarize.
     */
    protected function summarize(EntityInterface $entity)
    {
        $this->display->section('Summary of "'.$entity->getName().'"');

        $this->listFields($entity);
        $this->listAssociations($entity);
    }

    /* Fields */

    /**
     * Interaction flow to edit existing fields.
     *
     * @param EntityInterface $entity The entity being defined.
     */
    protected function editFields(EntityInterface $entity)
    {
        while (true) {
            $this->listFields($entity);

            if (false === $name = $this->selectField($entity)) {
                break;
            }

            $this->editField($entity, $name);
        }
    }

    /**
     * Asks the user to select a field name.
     *
     * @param EntityInterface $entity The entity owning the fields.
     *
     * @return string The answered field name.
     */
    protected function selectField(EntityInterface $entity)
    {
        $choice = $this->display->choice(
            'Select the <comment>field</comment> you want to <comment>edit</comment>',
            array_merge([static::CHOICE_SKIP], $entity->getFieldNamesWithoutPK()),
            static::CHOICE_SKIP
        );

        return static::CHOICE_SKIP === $choice ? false : $choice;
    }

    /**
     * Interaction flow to edit one field.
     *
     * @param EntityInterface $entity The entity being defined.
     * @param string          $name   The field name to edit.
     */
    protected function editField(EntityInterface $entity, $name)
    {
        $field = $entity->getField($name);

        $this->fieldHelper->askField($field);

        $this->display->success(sprintf(
            'The field "%s" was successfully updated !',
            $field->getName())
        );
    }

    /**
     * Interaction flow to add new fields.
     *
     * @param EntityInterface $entity The entity being defined.
     */
    protected function addFields(EntityInterface $entity)
    {
        while (true) {
            $this->listFields($entity);

            if (!$this->display->confirm(
                'Do you want to <comment>add</comment> a new <comment>field</comment> ?',
                false
            )) {
                break;
            }

            $this->addField($entity);
        }
    }

    /**
     * Interaction flow to add one field.
     *
     * @param EntityInterface $entity The entity being defined.
     */
    protected function addField(EntityInterface $entity)
    {
        $field = new Field();
        $entity->addField($field);

        $this->fieldHelper->askField($field);

        $this->display->success(sprintf(
            'The field "%s" was successfully created !',
            $field->getName())
        );
    }

    /**
     * Displays a summary of all the fields.
     *
     * @param EntityInterface $entity The entity owning the fields.
     */
    protected function listFields(EntityInterface $entity)
    {
        $this->display->text('List of mapped fields:');

        $headers = ['Name', 'Type', 'Nullable', 'Unique'];

        $rows = [];
        foreach ($entity->getFields() as $field) {
            $type = $field->getType();

            if ($field instanceof PrimaryKeyInterface) {
                $type .= ' (<comment>PK</comment>)';
            } elseif ('string' === $field->getType()) {
                $type .= sprintf(' (<comment>%s</comment>)', $field->getLength());
            } elseif ('decimal' === $field->getType()) {
                $type .= sprintf(
                    ' (<comment>%s</comment>, <comment>%s</comment>)',
                    $field->getPrecision(),
                    $field->getScale()
                );
            }

            $rows[] = [
                $field->getName(),
                $type,
                var_export($field->isNullable(), true),
                var_export($field->isUnique(), true),
            ];
        }

        $this->display->table($headers, $rows);
    }

    /**
     * Sets the FieldHelperInterface to use with this helper.
     *
     * @param FieldHelperInterface $fieldHelper
     *
     * @return self
     */
    public function setFieldHelper(FieldHelperInterface $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;

        return $this;
    }

    /* Associations */

    /**
     * Interaction flow to edit existing associations.
     *
     * @param EntityInterface $entity The owning entity.
     */
    protected function editAssociations(EntityInterface $entity)
    {
        while (true) {
            $this->listAssociations($entity);

            if (false === $name = $this->selectAssociation($entity)) {
                break;
            }

            $this->editAssociation($entity, $name);
        }
    }

    /**
     * Asks the user to select an association name.
     *
     * @param EntityInterface $entity The entity owning the associations.
     */
    protected function selectAssociation(EntityInterface $entity)
    {
        $choice = $this->display->choice(
            'Select the <comment>association</comment> you want to <comment>edit</comment>',
            array_merge([static::CHOICE_SKIP], $entity->getAssociationNames()),
            static::CHOICE_SKIP
        );

        return static::CHOICE_SKIP === $choice ? false : $choice;
    }

    /**
     * Interaction flow to edit one association.
     *
     * @param EntityInterface $entity The entity being defined.
     * @param string          $name   The association name to edit.
     */
    protected function editAssociation(EntityInterface $entity, $name)
    {
        $association = $entity->getAssociation($name);

        $this->associationHelper->askAssociation($association);

        $this->display->success(sprintf(
            'The association "%s" was successfully updated !',
            $association->getName())
        );
    }

    /**
     * Interaction flow to add new associations.
     *
     * @param EntityInterface $entity
     */
    protected function addAssociations(EntityInterface $entity)
    {
        while (true) {
            $this->listAssociations($entity);

            if (!$this->display->confirm(
                'Do you want to <comment>add</comment> a new <comment>association</comment> ?',
                false
            )) {
                break;
            }

            $this->addAssociation($entity);
        }
    }

    /**
     * Interaction flow to add one association.
     *
     * @param EntityInterface $entity The entity beind defined.
     */
    protected function addAssociation(EntityInterface $entity)
    {
        $association = new Association();
        $entity->addAssociation($association);

        $this->associationHelper->askAssociation($association);

        $this->display->success(sprintf(
            'The association "%s" was successfully created !',
            $association->getName())
        );
    }

    /**
     * Displays a summary of all the associations.
     *
     * @param EntityInterface $entity The entity owning the associations.
     */
    protected function listAssociations(EntityInterface $entity)
    {
        if ($entity->getAssociations()->isEmpty()) {
            return;
        }

        $this->display->text('List of mapped associations:');

        $headers = ['Name', 'Type', 'Target entity'];

        $formatter = new OutputFormatter();

        $rows = [];
        foreach ($entity->getAssociations() as $association) {
            $type = $association->getType();

            $type .= $association->isBidirectional()
                ? ' bidirectional'
                : ' unidirectional';

            if ($association->isBidirectional()) {
                $type .= $association->isOwningSide()
                    ? sprintf(' inversed by "<comment>%s</comment>"', $association->getInversedBy())
                    : sprintf(' mapped by "<comment>%s</comment>"', $association->getMappedBy());
            }

            $namespace = explode('\\', $association->getTargetEntity());
            $shortName = sprintf('<comment>%s</comment>', array_pop($namespace));

            $rows[] = [
                $association->getName(),
                $type,
                $formatter->escape(implode('\\', $namespace).'\\').$shortName,
            ];
        }

        $this->display->table($headers, $rows);
    }

    /**
     * Sets the AssociationHelperInterface to use with this helper.
     *
     * @param AssociationHelperInterface $associationHelper
     *
     * @return self
     */
    public function setAssociationHelper(AssociationHelperInterface $associationHelper)
    {
        $this->associationHelper = $associationHelper;

        return $this;
    }
}

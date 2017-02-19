<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Command to generate entities.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class GenerateEntityCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('remg:generate:entity')
            ->setDescription('This command generates Doctrine2 entities.')
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity name to generate.');
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /* @var StyleInterface */
        $display = $this->getDisplay($input, $output);
        /* @var \Remg\GeneratorBundle\Command\Helper\EntityHelper */
        $helper = $this->getEntityHelper($display);
        /* @var \Remg\GeneratorBundle\Mapping\EntityFactory */
        $factory = $this->get('remg_generator.entity_factory');

        $display->title('** Welcome to the Doctrine2 Entity Generator **');
        $display->text([
            'This command helps you generate Doctrine2 entities for Symfony3 applications.',
            'You will be asked to define field and association mapping informations.',
            '',
        ]);

        $display->section('Define the name of the entity to generate:');
        $display->text([
            'You can use the shortcut notation (e.g., <info>AppBundle:Post</info>)',
            'or the fully qualified class name (e.g., <info>AppBundle\Entity\Post</info>).',
        ]);

        /* @var string $entityName The entity fully qualified class name to generate. */
        $entityName = $helper->askName($input->getArgument('entity'));
        /* @var \Remg\GeneratorBundle\Model\Entity The entity model. */
        $entity = $factory->createEntity($entityName);

        $display->success('Ready to build entity "'.$entity->getName().'".');
        $display->text('The primary key was added automatically (named <comment>id</comment>).');
        if (!$entity->getAssociations()->isEmpty()) {
            $display->text(sprintf(
                '<comment>%s association(s)</comment> have been detected and automaticly mapped.',
                $entity->getAssociations()->count()
            ));
        }

        // Interact with the user to build the entity.
        $helper->askEntity($entity);

        $input->setArgument('entity', $entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var StyleInterface */
        $display = $this->getDisplay($input, $output);
        /* @var \Remg\GeneratorBundle\Model\Entity The ready-to-generate entity model. */
        $entity = $input->getArgument('entity');

        /* @var Remg\GeneratorBundle\Generator\EntityGeneratorInterface $generator */
        $generator = $this->get('remg_generator.entity_generator');

        // Write the entity class.
        $generator->dump($entity);
        $display->success(sprintf('Entity "%s" generated successfully !', $entity->getName()));
    }

    /**
     * Returns a configured EntityHelperInterface instance.
     *
     * @param StyleInterface $display
     *
     * @return \Remg\GeneratorBundle\Command\Helper\EntityHelper
     */
    protected function getEntityHelper(StyleInterface $display)
    {
        $entityHelper = $this->get('remg_generator.entity_helper');
        $fieldHelper = $this->get('remg_generator.field_helper');
        $associationHelper = $this->get('remg_generator.association_helper');

        $entityHelper->setDisplay($display);
        $fieldHelper->setDisplay($display);
        $associationHelper->setDisplay($display);

        $entityHelper
            ->setFieldHelper($fieldHelper)
            ->setAssociationHelper($associationHelper);

        return $entityHelper;
    }
}

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

/**
 * Command to regenerate entities.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class RegenerateEntityCommand extends GenerateEntityCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('remg:regenerate:entity')
            ->setDescription('This command regenerates Doctrine2 entities.')
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity name to edit.');
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
            'This command helps you edit Doctrine2 entities for Symfony3 applications.',
            'You will be asked to define field and association mapping informations.',
            '',
        ]);
        $display->caution([
            'Caution is needed with this command, code can be lost.',
            'This command will regenerate an entity based on its metadata.
It means all custom code not generated by this tool will be lost.',
        ]);

        $display->section('Define the entity to edit:');

        /* @var array*/
        $entities = $factory->getAllEntities();

        /* @var string $entityName The entity fully qualified class name to generate. */
        $entityName = $helper->selectEntity($entities, $input->getArgument('entity'));
        /* @var \Remg\GeneratorBundle\Model\Entity The entity model. */
        $entity = $factory->getEntity($entityName);

        $display->success('Ready to edit entity "'.$entity->getName().'".');

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
        $display->success(sprintf('Entity "%s" re-generated successfully !', $entity->getName()));
    }
}

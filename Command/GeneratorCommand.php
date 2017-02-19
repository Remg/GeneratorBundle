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

use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Base class for all generator commands.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
abstract class GeneratorCommand extends ContainerAwareCommand
{
    /**
     * Contains an instance of a StyleInterface.
     *
     * @var \Symfony\Component\Console\Style\StyleInterface
     */
    private $display;

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Check that the connection with the database is established.
        try {
            $this->get('doctrine.orm.entity_manager')->getConnection()->ping();
        } catch (ConnectionException $exception) {
            $this->getDisplay($input, $output)->error([
                'There has been an error connecting to your database.',
                'Please check your connection parameters in app/config/parameters.yml.',
            ]);

            throw $exception;
        }
    }

    /**
     * Returns a StyleInterface.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return \Symfony\Component\Console\Style\StyleInterface
     */
    protected function getDisplay(InputInterface $input, OutputInterface $output)
    {
        if (!$this->display) {
            $this->display = new SymfonyStyle($input, $output);
        }

        return $this->display;
    }

    /**
     * Gets a container service by its id.
     *
     * @param string $serviceId The service id
     *
     * @return object The service
     */
    protected function get($serviceId)
    {
        return $this->getContainer()->get($serviceId);
    }
}

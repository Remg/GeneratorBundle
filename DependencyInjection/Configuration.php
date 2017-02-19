<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('remg_generator');

        $rootNode
            ->children()
                ->arrayNode('entity')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('configuration_format')
                            ->values(['annotation', 'yml', 'yaml', 'xml', 'php'])
                            ->defaultValue('annotation')
                        ->end()
                        ->booleanNode('generate_constraints')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

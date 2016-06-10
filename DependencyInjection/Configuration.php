<?php

// src/Acme/HelloBundle/DependencyInjection/Configuration.php
namespace Naldz\Bundle\FixturamaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fixturama');

        $rootNode
            ->children()
                ->scalarNode('dsn')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('schema_file')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
<?php

namespace Naldz\Bundle\FixturamaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class FixturamaExtension extends Extension
{
    /**
     * Loads the configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container, Processor $processor = null)
    {
        //load the service configuration
        $configuration = $this->getConfiguration($configs, $container);
        if (is_null($processor)) {
            $processor = new Processor();
        }
        
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('fixturama.dsn', $config['dsn']);
        $container->setParameter('fixturama.schema', $config['schema']);

        //load the services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

    }
}
<?php

namespace Naldz\Bundle\FixturamaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaConfiguration;
use Symfony\Component\Yaml\Yaml;

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
        
        $configOptions = $processor->processConfiguration($configuration, $configs);
        $container->setParameter('fixturama.dsn', $configOptions['dsn']);
        $container->setParameter('fixturama.schema_file', $configOptions['schema_file']);

        //process the schema_file configuration
        $schemaConfiguration = new SchemaConfiguration();
        $schemaFileContent = Yaml::parse(file_get_contents($configOptions['schema_file']));
        $schemaConfigOptions = $processor->processConfiguration($schemaConfiguration, $schemaFileContent);

        $container->setParameter('fixturama.model_definition', $schemaConfigOptions);

        //load the services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
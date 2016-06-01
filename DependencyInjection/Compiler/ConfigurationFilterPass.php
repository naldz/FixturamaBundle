<?php

namespace Naldz\Bundle\FixturamaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Naldz\Bundle\FixturamaBundle\Fixturama\SchemaConfiguration;

class ConfigurationFilterPass implements CompilerPassInterface
{
    private $fs = null;
    private $yamlParser = null;
    private $schemaFileObject = null;
    private $schemaConfiguration = null;
    private $processor = null;

    public function __construct(Filesystem $fs = null, YamlParser $yamlParser = null, \SplFileObject $schemaFileObject = null, SchemaConfiguration $schemaConfiguration = null, Processor $processor = null)
    {
        if (is_null($fs)) {
            $fs = new Filesystem();
        }
        $this->fs = $fs;

        if (is_null($yamlParser)) {
            $yamlParser = new YamlParser();
        }
        $this->yamlParser = $yamlParser;
        $this->schemaFileObject = $schemaFileObject;

        if (is_null($schemaConfiguration)) {
            $schemaConfiguration = new SchemaConfiguration();
        }
        $this->schemaConfiguration = $schemaConfiguration;

        if (is_null($processor)) {
            $processor = new Processor();
        }
        $this->processor = $processor;
    }

    public function process(ContainerBuilder $container)
    {
        $schemaFilePath = $container->getParameterBag()->resolveValue($container->getParameter('fixturama.schema'));

        if (!$this->fs->exists($schemaFilePath)) {
            throw new InvalidConfigurationException(sprintf('Unable to locate fixturama schema in "%s"', $schemaFilePath));
        }

        $schemaFileObject = $this->schemaFileObject;
        if (is_null($schemaFileObject)) {
            $schemaFileObject = new \SplFileObject($schemaFilePath, 'r');
        }

        $schemaFileContent = $schemaFileObject->fread($schemaFileObject->getSize());
        $schema = $this->yamlParser->parse($schemaFileContent);
        $processedConfig = $this->processor->processConfiguration($this->schemaConfiguration, $schema);
        $container->setParameter('fixturama.model_definition', $processedConfig);
    }
}
<?php

namespace Naldz\Bundle\FixturamaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaConfiguration;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConfigurationFilterPass implements CompilerPassInterface
{
    private $fs = null;
    
    public function __construct(Filesystem $fs = null)
    {
        if (is_null($fs)) {
            $fs = new Filesystem();
        }
        $this->fs = $fs;
    }

    public function process(ContainerBuilder $container)
    {
        $schemaFilePath = $container->getParameterBag()->resolveValue($container->getParameter('fixturama.schema_file'));
        if (!$this->fs->exists($schemaFilePath)) {
            throw new InvalidConfigurationException(sprintf('Unable to locate fixturama schema in "%s"', $schemaFilePath));
        }
    }
}
<?php

namespace Naldz\Bundle\FixturamaBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Naldz\Bundle\FixturamaBundle\DependencyInjection\Compiler\ConfigurationFilterPass;

class FixturamaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationFilterPass());
    }
}
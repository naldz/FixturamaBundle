<?php

namespace Naldz\Bundle\FixturamaBundle\TestHelper\App;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Bazinga\Bundle\FakerBundle\BazingaFakerBundle(),
            new \Naldz\Bundle\FixturamaBundle\FixturamaBundle(),
            new \Naldz\Bundle\DsnParserBundle\DsnParserBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
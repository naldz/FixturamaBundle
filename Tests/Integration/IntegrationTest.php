<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Naldz\Bundle\FixturamaBundle\TestHelper\App\AppKernel;


class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $appRoot;
    protected $kernel;
    protected $application;
    protected $commandExecutor;
    protected $env;
    protected $patchDir;

    public function setUp()
    {
        $this->kernel = new AppKernel($this->env, true);
        $this->appRoot = __DIR__.'/../../../TestHelper/App';
        //boot the kernel
        $this->kernel->boot();

        //remove the cache files from the app
        $this->fs = new FileSystem();
        $this->fs->remove(array($this->appRoot.'/cache', $this->appRoot.'/logs'));

    }

    public function testServicesAreDefined()
    {
        $container = $this->kernel->getContainer();
        $this->assertNotNull($container->get('fixturama.faker.generator'));
        $this->assertNotNull($container->get('fixturama.yaml.parser'));
        $this->assertNotNull($container->get('fixturama.generator'));
        $this->assertNotNull($container->get('fixturama.model_fixture_generator'));
    }

    public function testModelDefinitionParameterIsDefinedInContainer()
    {
        $container = $this->kernel->getContainer();
        $this->assertNotNull($container->getParameter('fixturama.model_definition', null));
    }
}
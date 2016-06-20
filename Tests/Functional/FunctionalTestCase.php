<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Naldz\Bundle\FixturamaBundle\TestHelper\App\AppKernel;

class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    protected $appRoot;
    protected $kernel;
    protected $application;
    protected $commandExecutor;
    protected $env;
    protected $patchDir;
    protected $pdo;

    public function setUp()
    {
        $this->kernel = new AppKernel($this->env, true);
        $this->appRoot = __DIR__.'/../../TestHelper/App';
        $this->application = new Application($this->kernel);
        //boot the kernel
        $this->kernel->boot();
        //remove the cache files from the app
        $this->fs = new FileSystem();
        $this->fs->remove(array($this->appRoot.'/cache', $this->appRoot.'/logs'));
    }
}
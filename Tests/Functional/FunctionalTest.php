<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Naldz\Bundle\FixturamaBundle\TestHelper\App\AppKernel;

class FunctionalTest extends \PHPUnit_Framework_TestCase
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
        $this->appRoot = __DIR__.'/../../TestHelper/App';
        //boot the kernel
        $this->kernel->boot();
        //remove the cache files from the app
        $this->fs = new FileSystem();
        $this->fs->remove(array($this->appRoot.'/cache', $this->appRoot.'/logs'));

        $pdo = $this->kernel->getContainer()->get('fixturama.pdo');

        //DROP the test database
        $pdo->exec('DROP DATABASE IF EXISTS `fixturama`');
        //create the database
        $pdo->exec('CREATE DATABASE `fixturama`');
        $pdo->exec('CREATE TABLE `fixturama`.`table1` (
            `field1` int(5) unsigned NOT NULL AUTO_INCREMENT,
            `field2` varchar(100) NOT NULL,
            PRIMARY KEY (`field1`)
        ) ENGINE=InnoDB;');
    }

    public function testFixtureLoading()
    {
        $container = $this->kernel->getContainer();
        $fixturama = $container->get('fixturama.manager');
        $fixturama->setUp(array(
            'fixturama.table1' => array(
                array('field1' => 101),
                array('field2' => 'This is a preset text.')
            )
        ));
    }

}
<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Functional;

use Naldz\Bundle\FixturamaBundle\Tests\Functional\FunctionalTestCase;

class FunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->pdo = $this->kernel->getContainer()->get('fixturama.pdo');
        //DROP the test database
        $this->pdo->exec('DROP DATABASE IF EXISTS `fixturama1`');

        //create the database
        $this->pdo->exec('CREATE DATABASE `fixturama1`');
        $this->pdo->exec('CREATE TABLE `fixturama1`.`table1` (
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
            'fixturama1.table1' => array(
                array('field1' => 101),
                array('field2' => 'This is a preset text.')
            )
        ));

        //check that the fixture data was inserted in database
        $stmt = $this->pdo->query('SELECT * FROM `fixturama1`.`table1`;');
        $dataset = $stmt->fetchAll();
        $this->assertEquals(2, count($dataset));
        $this->assertEquals(101, $dataset[0]['field1']);
        $this->assertEquals('This is a preset text.', $dataset[1]['field2']);
    }

}
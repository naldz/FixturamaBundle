<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Functional;

use Naldz\Bundle\FixturamaBundle\Tests\Functional\FunctionalTestCase;
use Naldz\Bundle\FixturamaBundle\Command\SchemaDiffCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SchemDiffCommandTest extends FunctionalTestCase
{
    // protected $commandExecutor = null;

    // public function setUp()
    // {
    //     parent::setUp();
    //     $this->commandExecutor
    // }


    // public function testFixtureLoading()
    // {
    //     $container = $this->kernel->getContainer();
    //     $fixturama = $container->get('fixturama.manager');
    //     $fixturama->setUp(array(
    //         'fixturama.table1' => array(
    //             array('field1' => 101),
    //             array('field2' => 'This is a preset text.')
    //         )
    //     ));

    //     //check that the fixture data was inserted in database
    //     $stmt = $this->pdo->query('SELECT * FROM `fixturama`.`table1`;');
    //     $dataset = $stmt->fetchAll();
    //     $this->assertEquals(2, count($dataset));
    //     $this->assertEquals(101, $dataset[0]['field1']);
    //     $this->assertEquals('This is a preset text.', $dataset[1]['field2']);
    // }

    public function testMissingOrmSchemaThrowsException()
    {
        $this->setExpectedException('Symfony\Component\Filesystem\Exception\IOException');
        $this->application->add(new SchemaDiffCommand());
        $command = $this->application->find('fixturama:schema-diff');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'           => $command->getName(),
            'orm-schema-paths'   => $this->appRoot.'/config/unknown_schema.xml'
        ));
    }

    public function testDiffProcess()
    {
        $this->application->add(new SchemaDiffCommand());
        $command = $this->application->find('fixturama:schema-diff');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'            => $command->getName(),
            'orm-schema-paths'   => $this->appRoot.'/config/propel_schema1.xml '.$this->appRoot.'/config/propel_schema2.xml'
        ));
        $output = $commandTester->getDisplay();
        $outputArray = json_decode($output, true);

        $expectedOutputArray = array(
            'added_databases'   => array('fixturama3'),
            'removed_databases' => array('fixturama2'),
            'added_tables'      => array('fixturama1.table3'),
            'removed_tables'    => array('fixturama1.table1'),
            'added_fields'      => array('fixturama1.table2.field3'),
            'removed_fields'    => array('fixturama1.table2.field2')
        );

        $this->assertEquals($expectedOutputArray, $outputArray);
    }
}
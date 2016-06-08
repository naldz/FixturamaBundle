<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Unit\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\SqlConverter;

class SqlConverterTest extends \PHPUnit_Framework_TestCase
{
    private $schemaDefinitionMock = null;

    public function setUp(){
        $this->schemaDefinitionMock = $this->createSchemaDefinitionMock(array(
            'database.blog_post' => array(
                'fields' => array(
                    'id' => array(
                        'type'=> 'numberBetween', 
                        'params' => array(0, 99999)
                    ),
                    'title' => array(
                        'type' => 'sentence',
                        'params' => array(10, true)
                    )
                )
            )
        ));
    }

    public function testSuccessfulConversion()
    {
        $pdoMock = $this->createPdoMock(array(
            '1'             => "'1'",
            'field1_2 value'=> "'field1_2 value'",
            '2'             => "'2'",
            'field2_2 value'=> "'field2_2 value'"
        ));
        
        $data = array(
            array('id' => '1', 'title' => 'field1_2 value'),
            array('id' => '2', 'title' => 'field2_2 value')
        );

        $sqlConverter = new SqlConverter($this->schemaDefinitionMock, $pdoMock);
        $actualSql = $sqlConverter->convert('database.blog_post', $data);
        $expectedSql = "INSERT INTO database.blog_post (`id`,`title`) VALUES ('1','field1_2 value'),('2','field2_2 value');";
        $this->assertEquals($actualSql, $expectedSql);
    }

    private function createPdoMock($data = array())
    {
        $mock = $this->getMockBuilder('Naldz\Bundle\TestUtilityBundle\Pdo\Mock\MockablePdo')
            ->getMock();

        $mock->expects($this->any())
            ->method('quote')
            ->will(
                $this->returnCallback(
                function($rawValue) use ($data) {
                    return $data[$rawValue];
                }
            ));

        return $mock;
    }

    private function createSchemaDefinitionMock($data = array())
    {
        $mock = $this->getMockBuilder('Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaDefinition')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getModelDefinition')
            ->will($this->returnCallback(
                function($modelName) use ($data) {
                    if (array_key_exists($modelName, $data)) {
                        if ($data[$modelName] instanceof \Exception) {
                            throw $data[$modelName];
                        }
                        return $data[$modelName];
                    }
                }
            ));

        return $mock;
    }
}

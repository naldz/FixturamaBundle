<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Unit\Fixturama\Schema;

use Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaDefinition;

class SchemaDefinitionTest extends \PHPUnit_Framework_TestCase
{
    private $rawDefinition = null;
    private $modelDefinition = null;
    private $idFieldDefinition = array('type' => 'numberBetween', 'params' => array(0, 9999));
    private $titleFieldDefinition = array('type' => 'sentence', 'params' => array(10, true));
    private $schemaDefinition = null;

    public function setUp()
    {
        $this->modelDefinition = array(
            'fields' => array(
                'id' => $this->idFieldDefinition,
                'title' => $this->titleFieldDefinition
            )
        );
        $this->rawDefinition = array(
            'models' => array(
                'blog_author' => $this->modelDefinition
            )
        );
        $this->schemaDefinition = new SchemaDefinition($this->rawDefinition);
    }

    public function testUnknownModelNameThrowsException()
    {
        $this->setExpectedException('Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException');
        $this->schemaDefinition->getModelDefinition('unknown_model');
    }

    public function testUnknownModelFieldNameThrowsException()
    {
        $this->setExpectedException('Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelFieldException');
        $this->schemaDefinition->getModelFieldDefinition('blog_author', 'unknown_field');
    }

    public function testUnknownModelWhileGettingFieldThrowsException()
    {
        $this->setExpectedException('Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException');
        $this->schemaDefinition->getModelFieldDefinition('unknown_model', 'title');
    }

    public function testSuccessfulGettingOfModelDefinition()
    {
        $actualModelDefinition = $this->schemaDefinition->getModelDefinition('blog_author');
        $expectedModelDefinition = $this->modelDefinition;
        $this->assertEquals($expectedModelDefinition, $actualModelDefinition);
    }

    public function testSuccessfulGettingOfModelFieldDefinition()
    {
        $actualModelFieldDefinition = $this->schemaDefinition->getModelFieldDefinition('blog_author', 'title');
        $expectedModelFieldDefinition = $this->titleFieldDefinition;
        $this->assertEquals($expectedModelFieldDefinition, $actualModelFieldDefinition);
    }

    public function testGettingOfModelNames()
    {
        $schemaDefinition = new SchemaDefinition($this->rawDefinition);
        $actualModelNames = $schemaDefinition->getModelNames();
        $expectedModelNames = array('blog_author');
        $this->assertEquals($expectedModelNames, $actualModelNames);
    }
}
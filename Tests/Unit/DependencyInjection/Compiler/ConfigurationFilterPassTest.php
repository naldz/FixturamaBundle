<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Unit\DependencyInjection\Compiler;

use Naldz\Bundle\FixturamaBundle\DependencyInjection\FixturamaExtension;
use Naldz\Bundle\FixturamaBundle\DependencyInjection\Compiler\ConfigurationFilterPass;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationFilterPassTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $container;
    private $extension;

    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        $this->container = new ContainerBuilder();
        $this->extension = new FixturamaExtension();
    }

    public function testNormalConfig()
    {
        $configArray = array();

        $fsMock = $this->createFileSystemMock(true);
        $yamlParserMock = $this->createYamlParserMock(array());
        $fileObjectMock = $this->createFileObjectMock();
        $schemaConfigurationMock = $this->createSchemaConfigurationMock();
        $processorMock = $this->createProcessorMock($schemaConfigurationMock, $configArray);

        $configFilterPass = new ConfigurationFilterPass(
            $fsMock, 
            $yamlParserMock, 
            $fileObjectMock, 
            $schemaConfigurationMock, 
            $processorMock
        );
        $this->container->addCompilerPass($configFilterPass);

        $options = array('schema' => '/sample/schema/path', 'dsn' => 'mysql:@bla');
        $config = array('fixturama' => $options);
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testInvalidSchemaLocationThrowsException()
    {
        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $fsMock = $this->createFileSystemMock(false);
        $yamlParserMock = $this->createYamlParserMock(array());
        $fileObjectMock = $this->createFileObjectMock();
        $schemaConfigurationMock = $this->createSchemaConfigurationMock();
        $processorMock = $this->createProcessorMock($schemaConfigurationMock, array());

        $configFilterPass = new ConfigurationFilterPass(
            $fsMock, 
            $yamlParserMock, 
            $fileObjectMock, 
            $schemaConfigurationMock, 
            $processorMock
        );
        $this->container->addCompilerPass($configFilterPass);

        $options = array('schema' => '/path/nowhere', 'dsn' => 'mysql:@bla');
        $config = array('fixturama' => $options);
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    //Test that whatever exception thrown by the YamlParser is bubbled up
    public function testInvalidYamlConfigurationThrowsException()
    {
        $this->setExpectedException('Symfony\Component\Yaml\Exception\ParseException');

        $fsMock = $this->createFileSystemMock(true);
        $yamlParserMock = $this->createYamlParserMock(null, new \Symfony\Component\Yaml\Exception\ParseException('Test Exception'));
        $fileObjectMock = $this->createFileObjectMock();
        $schemaConfigurationMock = $this->createSchemaConfigurationMock();
        $processorMock = $this->createProcessorMock($schemaConfigurationMock, array());

        $configFilterPass = new ConfigurationFilterPass(
            $fsMock, 
            $yamlParserMock, 
            $fileObjectMock, 
            $schemaConfigurationMock, 
            $processorMock
        );
        $this->container->addCompilerPass($configFilterPass);

        $options = array('schema' => '/path/nowhere', 'dsn' => 'mysql:@bla');
        $config = array('fixturama' => $options);
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    //Test that whatever exception thrown by the configuration processor is bubbled up
    public function testFailedConfigurationProcessingThrowsException()
    {
        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $fsMock = $this->createFileSystemMock(true);
        $yamlParserMock = $this->createYamlParserMock(array());
        $fileObjectMock = $this->createFileObjectMock();

        $schemaConfigurationMock = $this->createSchemaConfigurationMock();
        $processorMock = $this->createProcessorMock($schemaConfigurationMock, array(), new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException('Test Exception'));

        $configFilterPass = new ConfigurationFilterPass(
            $fsMock, 
            $yamlParserMock, 
            $fileObjectMock, 
            $schemaConfigurationMock, 
            $processorMock
        );
        $this->container->addCompilerPass($configFilterPass);

        $options = array('schema' => '/path/nowhere', 'dsn' => 'mysql:@bla');
        $config = array('fixturama' => $options);
        $this->extension->load($config, $this->container);
        $this->container->compile();
    }

    public function testModelDefinitionIsSet()
    {
        $schemaDef = array(
            'schema' => array(
                'database' => 'test_db',
                'models' => array(
                    'blog' => array(
                        'fields' => array(
                            'id' => array(
                                'type'=> 'numberBetween', 
                                'params' => array(0, 99999)
                            )
                        )
                    )
                )
            )
        );

        $fsMock = $this->createFileSystemMock(true);
        $yamlParserMock = $this->createYamlParserMock($schemaDef);
        $fileObjectMock = $this->createFileObjectMock();

        $schemaConfigurationMock = $this->createSchemaConfigurationMock();

        $processorMock = $this->createProcessorMock($schemaConfigurationMock, $schemaDef);

        $configFilterPass = new ConfigurationFilterPass(
            $fsMock, 
            $yamlParserMock, 
            $fileObjectMock, 
            $schemaConfigurationMock, 
            $processorMock
        );
        $this->container->addCompilerPass($configFilterPass);

        $options = array('schema' => '/path/nowhere', 'dsn' => 'mysql:@bla');
        $config = array('fixturama' => $options);
        $this->extension->load($config, $this->container);
        $this->container->compile();

        $this->assertEquals($schemaDef, $this->container->getParameter('fixturama.model_definition'));
    }

    private function createFileSystemMock($returnValue) 
    {
        $fsMock = $this->getMock('Symfony\\Component\\Filesystem\\Filesystem');
        $fsMock->expects($this->once())
            ->method('exists')
            ->will($this->returnValue($returnValue));

        return $fsMock;
    }

    private function createYamlParserMock($config, $exception = null) 
    {
        $yamlParserMock = $this->getMock('Symfony\Component\Yaml\Parser');

        if (is_null($exception)) {
            $yamlParserMock->expects($this->any())
                ->method('parse')
                ->will($this->returnValue($config));
        }
        else {
            $yamlParserMock->expects($this->any())
                ->method('parse')
                ->will($this->throwException($exception));
        }
        return $yamlParserMock;
    }

    private function createFileObjectMock()
    {
        $fileObjectMock = $this->getMockBuilder('\SplFileObject')
            ->setConstructorArgs(['/dev/null'])
            ->getMock();
        $fileObjectMock->expects($this->any())
            ->method('fread');
        $fileObjectMock->expects($this->any())
            ->method('getSize');

        return $fileObjectMock;
    }

    private function createSchemaConfigurationMock()
    {
        return $this->getMock('Naldz\Bundle\FixturamaBundle\Fixturama\Schema\SchemaConfiguration');
    }

    private function createProcessorMock($schemaConfiguration, $configArray, $exception = null)
    {
        $processorMock = $this->getMock('Symfony\Component\Config\Definition\Processor');
        if (is_null($exception)) {
            $processorMock->expects($this->any())
                ->method('processConfiguration')
                ->with($schemaConfiguration, $configArray)
                ->will($this->returnValue($configArray));
        }
        else {
            $processorMock->expects($this->any())
                ->method('processConfiguration')
                ->with($schemaConfiguration, $configArray)
                ->will($this->throwException($exception));
        }

        return $processorMock;
    }
}

<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Unit\DependencyInjection;

use Naldz\Bundle\FixturamaBundle\DependencyInjection\FixturamaExtension;
use Naldz\Bundle\FixturamaBundle\DependencyInjection\Compiler\ConfigurationFilterPass;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class FixturamaExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $container;
    
    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        $this->container = new ContainerBuilder();
    }

    //Test the whatever error is encountered by processor is bubbled up
    public function testErrorInProcessingConfigurationThrowsException()
    {
        $extension = new FixturamaExtension();
        $config = array('fixturama' => array());
        $processorMock = $this->createProcessorMock(array(), new InvalidConfigurationException('Test Exception'));

        try {
            $extension->load($config, $this->container, $processorMock);
            $this->container->compile();
            $this->fail('Expected InvalidConfigurationException was not thrown.');
        }
        catch (InvalidConfigurationException $e) {
            $this->assertContains('Test Exception', $e->getMessage());
        }
    }


    private function createProcessorMock($configArray, $exception = null)
    {
        $processorMock = $this->getMock('Symfony\Component\Config\Definition\Processor');
        if (is_null($exception)) {
            $processorMock->expects($this->any())
                ->method('processConfiguration')
                ->will($this->returnValue($configArray));
        }
        else {
            $processorMock->expects($this->any())
                ->method('processConfiguration')
                ->will($this->throwException($exception));
        }

        return $processorMock;
    }
}

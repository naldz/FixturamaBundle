<?php

namespace Naldz\Bundle\FixturamaBundle\Tests\Unit\Fixturama;

use Naldz\Bundle\FixturamaBundle\Fixturama\FixtureGenerator;
use Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelFieldException;

class FixtureGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $definition = array(
        'models' => array(
            'blog_author' => array(
                'fields' => array(
                    'id' => array(
                        'type'=> 'numberBetween', 
                        'params' => array(0, 99999)
                    ),
                    'name' => array(
                        'type' => 'name'
                    )
                )
            ),
            'blog_post' => array(
                'fields' => array(
                    'id' => array(
                        'type'=> 'numberBetween', 
                        'params' => array(0, 99999)
                    ),
                    'blog_author_id' => array(
                        'type'=> 'numberBetween', 
                        'params' => array(0, 99999)
                    ),
                    'title' => array(
                        'type' => 'sentence',
                        'params' => array(10, true)
                    ),
                    'tag' => array(
                        'type' => 'word'
                    )
                )
            )
        )
    );

    public function testGettingOfUnknownModelThrowsException()
    {
        $this->setExpectedException('Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelException');
        $modelFixtureGeneratorMock = $this->createModelFixtureGeneratorMock();
        $sut = new FixtureGenerator($this->definition, $modelFixtureGeneratorMock);

        $fixtureModel = $sut->generate(array(
            'UnknownModel' => array(array('field1' => 1, 'field2' => 2))
        ));
    }

    public function testModelFixtureGeneratorErrorThrowsException()
    {
        $this->setExpectedException('Naldz\Bundle\FixturamaBundle\Fixturama\Exception\UnknownModelFieldException');
        $modelFixtureGeneratorMock = $this->createModelFixtureGeneratorMock(array(
            new UnknownModelFieldException('Fake UnknownModelFieldException.'),
        ));
        $sut = new FixtureGenerator($this->definition, $modelFixtureGeneratorMock);

        $fixtureModel = $sut->generate(array(
            'blog_author' => array(array('unknown_field1' => 1, 'unknown_field2' => 2))
        ));
    }

    public function testSuccessfulGenerationOfFixtureData()
    {
        $modelFixtureGeneratorMock = $this->createModelFixtureGeneratorMock(array(
            array('id' => 1, 'name' => 'Author One'),
            array('id' => 2, 'name' => 'Author Two'),
            array('id' => 1, 'blog_author_id' => 1, 'title' => 'Post 1', 'tag' => 'tag_one'),
            array('id' => 2, 'blog_author_id' => 2, 'title' => 'Post 2', 'tag' => 'tag_two')
        ));
        $sut = new FixtureGenerator($this->definition, $modelFixtureGeneratorMock);
        $actualFixtureData = $sut->generate(array(
            'blog_author' => array(
                array('id' => 1), 
                array('id' => 2)
            ),
            'blog_post' => array(
                array('id' => 1, 'blog_author_id' => 1),
                array('id' => 2, 'blog_author_id' => 2)
            ),
        ));

        $expectedFixtureData = array(
            'blog_author' => array(
                array('id' => 1, 'name' => 'Author One'),
                array('id' => 2, 'name' => 'Author Two')
            ),
            'blog_post' => array(
                array('id' => 1, 'blog_author_id' => 1, 'title' => 'Post 1', 'tag' => 'tag_one'),
                array('id' => 2, 'blog_author_id' => 2, 'title' => 'Post 2', 'tag' => 'tag_two')
            )
        );
        $this->assertEquals($expectedFixtureData, $actualFixtureData);
    }

    private function createModelFixtureGeneratorMock($results = array())
    {
        $mock = $this->getMockBuilder('Naldz\Bundle\FixturamaBundle\Fixturama\ModelFixtureGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($results as $index => $result) {
            if ($result instanceof \Exception) {
                $mock->expects($this->at($index))
                    ->method('generate')
                    ->will($this->throwException($result));
            }
            else {
                $mock->expects($this->at($index))
                    ->method('generate')
                    ->will($this->returnValue($result));
            }
        }

        return $mock;
    }
}

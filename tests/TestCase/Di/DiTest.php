<?php

namespace RochaMarcelo\CakePimpleDi\Test\TestCase\Di;

use Cake\TestSuite\TestCase;
use RochaMarcelo\CakePimpleDi\Di\Di;

/**
 * RochaMarcelo\CakePimpleDi\Di\Di Test Case
 */
class DiTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Test = new Di();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Test);

        parent::tearDown();
    }

    /**
     * Test instance method
     *
     * @return void
     */
    public function testInstance()
    {
        $name = null;
        $default = Di::instance($name);
        $this->assertInstanceOf('RochaMarcelo\CakePimpleDi\Di\Di', $default);
        $default->set('new_info', 'new value info');
        $sameDefault = Di::instance($name);
        $this->assertSame($default, $sameDefault);

        $name = 'other';
        $other = Di::instance($name);
        $other->set('other_number', 150);
        $this->assertInstanceOf('RochaMarcelo\CakePimpleDi\Di\Di', $other);
        $this->assertNotSame($default, $other);
        $this->assertNotSame(Di::instance(null), $other);
        $sameOther = Di::instance($name);
        $this->assertSame($other, $sameOther);

        $name = 'something';
        $something = Di::instance($name);
        $other->set('service', function () {
            return new \stdClass;
        });
        $this->assertInstanceOf('RochaMarcelo\CakePimpleDi\Di\Di', $something);
        $this->assertNotSame($default, $something);
        $this->assertNotSame(Di::instance(null), $something);
        $this->assertNotSame($other, $something);
        $this->assertNotSame(Di::instance('other'), $something);
        $sameSomething = Di::instance($name);
        $this->assertSame($something, $sameSomething);

        $name = 'default';
        $default2 = Di::instance($name);
        $this->assertInstanceOf('RochaMarcelo\CakePimpleDi\Di\Di', $default2);

        $sameDefault2 = Di::instance($name);
        $this->assertSame($default2, $sameDefault2);
        $this->assertSame($default, $default2);
    }

    /**
     * Test set method
     *
     * @return void
     */
    public function testSet()
    {
        //String
        $Di = Di::instance();
        $Di->set('uuid', 'h2340sj2309');
        $actual = $Di->get('uuid');
        $expected = 'h2340sj2309';
        $this->assertSame($expected, $actual);

        //Normal
        $expected = new \stdClass;
        $expected->id = 103;
        $Di->set('service', function() {
            $job = new \stdClass;
            $job->id = 103;
            return $job;
        });
        $actual = $Di->get('service');
        $this->assertEquals($expected, $actual);

        $actual2 = $Di->get('service');
        $this->assertSame($actual2, $actual);

        //Factory
        $type = 'factory';
        $expected = new \stdClass;
        $expected->id = 150;
        $Di->set('service_2', function() {
            $job = new \stdClass;
            $job->id = 150;
            return $job;
        }, $type);
        $actual = $Di->get('service_2');
        $this->assertEquals($expected, $actual);

        $actual2 = $Di->get('service_2');
        $this->assertNotSame($actual2, $actual);

        //Parameters
        $type = 'parameter';
        $expected = function () {
            return rand();
        };

        $Di->set('random_func', $expected, $type);
        $actual = $Di->get('random_func');
        $this->assertSame($expected, $actual);

        $type = 'parameter';
        $expected = 'SESSION_ID';

        $Di->set('cookie_name', 'SESSION_ID', $type);
        $actual = $Di->get('cookie_name');
        $this->assertSame($expected, $actual);
    }

    public function testSetMany()
    {
        $services = [
            'BookLibrary\Client' => function() {
                return new \Cake\Network\Http\Client;
            },
            'BookLibrary\Finder' => function($c) {
                $finder = new \stdClass;
                $finder->client = $c['BookLibrary\Client'];
                return $finder;
            },
            'random_func' => [
                'value' => function () {
                    return rand();
                },
                'type' => 'parameter'
            ],
            'cookie_name' => 'SESSION_ID',
            [
                'id' => 'something',
                'value' => function () {
                    $std = new \stdClass;
                    $std->rand = rand();
                },
                'type' => 'factory'
            ]
        ];
        $Di = $this->getMockBuilder('RochaMarcelo\CakePimpleDi\Di\Di')->setMethods(['set'])->getMock();

        $Di->expects($this->at(0))
            ->method('set')
            ->with(
                $this->equalTo('BookLibrary\Client'),
                $this->equalTo(
                    function() {
                        return new \Cake\Network\Http\Client;
                    }
                ),
                null
            );

        $Di->expects($this->at(1))
            ->method('set')
            ->with(
                $this->equalTo('BookLibrary\Finder'),
                $this->equalTo(
                    function($c) {
                        $finder = new \stdClass;
                        $finder->client = $c['BookLibrary\Client'];
                        return $finder;
                    }
                ),
                null
            );

        $Di->expects($this->at(2))
            ->method('set')
            ->with(
                $this->equalTo('random_func'),
                $this->equalTo(
                    function () {
                        return rand();
                    }
                ),
                'parameter'
            );

        $Di->expects($this->at(3))
            ->method('set')
            ->with(
                $this->equalTo('cookie_name'),
                $this->equalTo('SESSION_ID'),
                null
            );

        $Di->expects($this->at(4))
            ->method('set')
            ->with(
                $this->equalTo('something'),
                $this->equalTo(
                    function () {
                        $std = new \stdClass;
                        $std->rand = rand();
                         return $std;
                    }
                ),
                'factory'
            );

        $Di->setMany($services);
    }
}
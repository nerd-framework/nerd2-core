<?php

namespace Nerd2\Proxy;

use PHPUnit\Framework\TestCase;

class ClassProxyTest extends TestCase
{
    /**
     * @var ClassProxyGenerator
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new ClassProxyGenerator(function (string $name, array $args) {
            switch ($name) {
                case 'bar':
                    return $args[0] + $args[1];
                default:
                    echo $name;
            }
        }, [FooInterface::class]);
    }

    public function testProxy()
    {
        $instance = $this->generator->newInstance();

        $this->assertInstanceOf(FooInterface::class, $instance);

        $this->expectOutputString('foo');
        $instance->foo();

        $sum = $instance->bar(5, 10);

        $this->assertEquals(15, $sum);
    }

    public function testProxyHelloWorld()
    {
        $hw = new HelloWorld();
        $proxy = ClassProxyGenerator::proxyObject($hw, function (string $name, array $args, \ReflectionMethod $method) use (&$proxy) {
            $method->setAccessible(true);
            echo "Before $name Call\n";
            $result = $method->invokeArgs($proxy, $args);
            echo "After $name Call\n";
            return $result;
        });

        $this->assertEquals('Hello, Bill! Bye, Bill!', $hw->hello('Bill'));
        $this->assertEquals('Hello, Bill! Bye, Bill!', $proxy->hello('Bill'));
    }
}

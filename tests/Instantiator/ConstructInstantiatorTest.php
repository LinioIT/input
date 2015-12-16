<?php
declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

class ConstructInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCreatingInstances()
    {
        $instantiator = new ConstructInstantiator();
        $instance = $instantiator->instantiate('ErrorException', ['foobar']);
        $this->assertInstanceOf('ErrorException', $instance);
        $this->assertEquals(new \ErrorException('foobar'), $instance);
    }
}

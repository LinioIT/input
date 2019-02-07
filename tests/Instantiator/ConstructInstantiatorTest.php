<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Linio\Component\Input\Constraint\Enum;
use PHPUnit\Framework\TestCase;

class ConstructInstantiatorTest extends TestCase
{
    public function testIsCreatingInstances(): void
    {
        $instantiator = new ConstructInstantiator();
        $instance = $instantiator->instantiate('ErrorException', ['foobar']);
        $this->assertInstanceOf('ErrorException', $instance);
        $this->assertEquals(new \ErrorException('foobar'), $instance);
    }

    public function testIsHandlingArraysWithStringKeys(): void
    {
        $instantiator = new ConstructInstantiator();
        $instance = $instantiator->instantiate(Enum::class, ['foo' => [1, 2], 'bar' => 'message']);
        $this->assertEquals(new Enum([1, 2], 'message'), $instance);
    }
}

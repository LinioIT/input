<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Linio\Component\Input\TestUser;
use PHPUnit\Framework\TestCase;

class SetInstantiatorTest extends TestCase
{
    public function testIsCreatingInstances(): void
    {
        $instantiator = new SetInstantiator();
        $instance = $instantiator->instantiate(TestUser::class, ['name' => 'foobar', 'age' => 20, 'is_active' => true]);
        $this->assertInstanceOf(TestUser::class, $instance);
        $this->assertEquals('foobar', $instance->getName());
        $this->assertEquals(20, $instance->getAge());
        $this->assertTrue($instance->isActive);
    }
}

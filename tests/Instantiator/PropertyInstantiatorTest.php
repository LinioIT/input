<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Linio\Component\Input\TestUser;
use PHPUnit\Framework\TestCase;

class PropertyInstantiatorTest extends TestCase
{
    public function testIsCreatingInstances(): void
    {
        $instantiator = new PropertyInstantiator();
        $instance = $instantiator->instantiate(TestUser::class, ['is_active' => true]);
        $this->assertInstanceOf(TestUser::class, $instance);
        $this->assertTrue($instance->isActive);
    }
}

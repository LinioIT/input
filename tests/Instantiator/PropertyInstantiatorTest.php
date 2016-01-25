<?php
declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Linio\Component\Input\TestUser;

class PropertyInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCreatingInstances()
    {
        $instantiator = new PropertyInstantiator();
        $instance = $instantiator->instantiate(TestUser::class, ['is_active' => true]);
        $this->assertInstanceOf(TestUser::class, $instance);
        $this->assertEquals(true, $instance->isActive);
    }
}

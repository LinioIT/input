<?php
declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Linio\Component\Input\Constraint\Enum;

class ConstructInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCreatingInstances()
    {
        $instantiator = new ConstructInstantiator();
        $instance = $instantiator->instantiate(Enum::class, ['foo' => [1, 2], 'bar' => 'message']);
        $this->assertEquals(new Enum([1, 2], 'message'), $instance);
    }
}

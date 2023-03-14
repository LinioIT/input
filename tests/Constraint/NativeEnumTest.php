<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use Linio\Component\Input\TestCase;

class NativeEnumTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new NativeEnum(FakeEnum::class);
        $this->assertFalse($constraint->validate('test'));
        $this->assertFalse($constraint->validate('blah'));

        $this->assertFalse($constraint->validate(['foo']));
        $obj = new \stdClass();
        $obj->var1 = 'foo';
        $this->assertFalse($constraint->validate($obj));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new NativeEnum(FakeEnum::class);
        $this->assertTrue($constraint->validate('FOO'));
        $this->assertTrue($constraint->validate('BAR'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new NativeEnum(FakeEnum::class);
        $this->assertFalse($constraint->validate('test'));
        $this->assertEquals('[["FOO","BAR"]] Invalid option for a native PHP enum. Allowed options are: ["FOO","BAR"]', $constraint->getErrorMessage('["FOO","BAR"]'));
    }
}

enum FakeEnum: string
{
    case Foo = 'FOO';
    case Bar = 'BAR';
}

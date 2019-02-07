<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class NotNullTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new NotNull();
        $this->assertFalse($constraint->validate(null));
        $this->assertFalse($constraint->validate(''));
        $this->assertFalse($constraint->validate(''));
        $this->assertFalse($constraint->validate('     '));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new NotNull();
        $this->assertTrue($constraint->validate(' test '));
        $this->assertTrue($constraint->validate(0));

        $this->assertTrue($constraint->validate(['']));
        $obj = new \stdClass();
        $obj->var1 = '';
        $this->assertTrue($constraint->validate($obj));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new NotNull();
        $this->assertFalse($constraint->validate(null));
        $this->assertEquals('[field] Unexpected empty content', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new NotNull('CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

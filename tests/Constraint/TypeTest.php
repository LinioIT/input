<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new Type('int');
        $this->assertFalse($constraint->validate('test'));
        $this->assertFalse($constraint->validate('2'));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new Type('int');
        $this->assertTrue($constraint->validate(2));
        $this->assertTrue($constraint->validate(123));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new Type('int');
        $this->assertFalse($constraint->validate('test'));
        $this->assertEquals('[field] Value does not match type: int', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new Type('int', 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

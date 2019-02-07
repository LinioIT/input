<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new Range(50, 100);
        $this->assertFalse($constraint->validate(120));
        $this->assertFalse($constraint->validate(101));
        $this->assertFalse($constraint->validate(null));

        $this->assertFalse($constraint->validate([75]));
        $obj = new \stdClass();
        $obj->var1 = 75;
        $this->assertFalse($constraint->validate($obj));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new Range(50, 100);
        $this->assertTrue($constraint->validate(50));
        $this->assertTrue($constraint->validate(85));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new Range(50, 100);
        $this->assertFalse($constraint->validate(14));
        $this->assertEquals('[field] Value is not between 50 and 100', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new Range(50, 100, 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

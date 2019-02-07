<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new DateTime();
        $this->assertFalse($constraint->validate('foobar@baz.com'));
        $this->assertFalse($constraint->validate('2018-01-99'));
        $this->assertFalse($constraint->validate(123));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new DateTime();
        $this->assertTrue($constraint->validate('2018-01-01'));
        $this->assertTrue($constraint->validate('2010-12-31T00:00:00+00:00'));
        $this->assertTrue($constraint->validate('2006-12-12 10:00:00.5'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new DateTime();
        $this->assertFalse($constraint->validate('foo/bar'));
        $this->assertEquals('[field] Invalid date/time format', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new DateTime('CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

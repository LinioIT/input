<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new DateRange('today', '+3 days');
        $this->assertFalse($constraint->validate('yesterday'));
        $this->assertFalse($constraint->validate('+8 days'));

        $this->assertFalse($constraint->validate(['now']));
        $obj = new \stdClass();
        $obj->var1 = 'now';
        $this->assertFalse($constraint->validate($obj));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new DateRange('today', '+3 days');
        $this->assertTrue($constraint->validate('today'));
        $this->assertTrue($constraint->validate('tomorrow'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new DateRange('today', '+3 days');
        $this->assertFalse($constraint->validate('yesterday'));
        $this->assertEquals('[field] Date is not between "today" and "+3 days"', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new DateRange('today', '+3days', 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

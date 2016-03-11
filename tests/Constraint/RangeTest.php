<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new Range(50, 100);
        $this->assertFalse($constraint->validate(120));
        $this->assertFalse($constraint->validate(101));
        $this->assertFalse($constraint->validate(null));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new Range(50, 100);
        $this->assertTrue($constraint->validate(50));
        $this->assertTrue($constraint->validate(85));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Range(50, 100);
        $this->assertFalse($constraint->validate(14));
        $this->assertEquals('[field] Value is not between 50 and 100', $constraint->getErrorMessage('field'));
    }
}

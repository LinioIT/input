<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class NotNullTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new NotNull();
        $this->assertFalse($constraint->validate(null));
        $this->assertFalse($constraint->validate(''));
        $this->assertFalse($constraint->validate(''));
        $this->assertFalse($constraint->validate('     '));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new NotNull();
        $this->assertTrue($constraint->validate(' test '));
        $this->assertTrue($constraint->validate(0));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new NotNull();
        $this->assertFalse($constraint->validate(null));
        $this->assertEquals('[field] Unexpected empty content', $constraint->getErrorMessage('field'));
    }
}

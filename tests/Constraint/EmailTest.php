<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new Email();
        $this->assertFalse($constraint->validate('foobar@bazcom'));
        $this->assertFalse($constraint->validate('foobar.com'));
        $this->assertFalse($constraint->validate('fooz@bar'));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new Email();
        $this->assertTrue($constraint->validate('foo@bar.com'));
        $this->assertTrue($constraint->validate('foo@bar.cl'));
        $this->assertTrue($constraint->validate('foo@bar.pe'));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Email();
        $this->assertFalse($constraint->validate('foobar.com'));
        $this->assertEquals('[field] Invalid email format', $constraint->getErrorMessage('field'));
    }
}

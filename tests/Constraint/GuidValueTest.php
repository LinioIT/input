<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class GuidValueTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new GuidValue();
        $this->assertFalse($constraint->validate('0dga84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca4b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-19d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-406-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-4b06-c87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-4b06-bc87-ab5ae3f5d4f'));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new GuidValue();
        $this->assertTrue($constraint->validate('0dca84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertTrue($constraint->validate('0DCA84B2-639D-4B06-BC87-7AB5AE3F5D4F'));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new GuidValue();
        $this->assertFalse($constraint->validate('0dga84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertEquals('Invalid GUID format', $constraint->getErrorMessage());
    }
}

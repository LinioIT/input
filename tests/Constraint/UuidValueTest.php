<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class UuidValueTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new UuidValue();
        $this->assertFalse($constraint->validate('0dga84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca4b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-19d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-406-bc87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-4b06-c87-7ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate('0dca84b2-639d-4b06-bc87-ab5ae3f5d4f'));
        $this->assertFalse($constraint->validate(null));
        $this->assertFalse($constraint->validate([]));
        $this->assertFalse($constraint->validate(new \stdClass()));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new UuidValue();
        $this->assertTrue($constraint->validate('0dca84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertTrue($constraint->validate('0DCA84B2-639D-4B06-BC87-7AB5AE3F5D4F'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new UuidValue();
        $this->assertFalse($constraint->validate('0dga84b2-639d-4b06-bc87-7ab5ae3f5d4f'));
        $this->assertEquals('[field] Invalid UUID format', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new UuidValue('CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

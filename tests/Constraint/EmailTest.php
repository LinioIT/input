<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new Email();
        $this->assertFalse($constraint->validate('foobar@bazcom'));
        $this->assertFalse($constraint->validate('foobar.com'));
        $this->assertFalse($constraint->validate('fooz@bar'));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new Email();
        $this->assertTrue($constraint->validate('foo@bar.com'));
        $this->assertTrue($constraint->validate('foo@bar.cl'));
        $this->assertTrue($constraint->validate('foo@bar.pe'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new Email();
        $this->assertFalse($constraint->validate('foobar.com'));
        $this->assertEquals('[field] Invalid email format', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new Email('CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class PatternTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertFalse($constraint->validate(null));
        $this->assertFalse($constraint->validate(' 2014-04-22 '));
        $this->assertFalse($constraint->validate('2014-04-2'));

        $this->assertFalse($constraint->validate(['2014-04-22']));
        $obj = new \stdClass();
        $obj->var1 = '2014-04-22';
        $this->assertFalse($constraint->validate($obj));
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertTrue($constraint->validate('2014-04-22'));
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertFalse($constraint->validate(null));
        $this->assertEquals('[field] Required pattern does not match', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/', 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

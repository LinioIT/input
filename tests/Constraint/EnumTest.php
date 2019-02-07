<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testIsCheckingInvalidData(): void
    {
        $constraint = new Enum(['foo', 'bar']);
        $this->assertFalse($constraint->validate('test'), 'The "test" value is not part of the Enum');
        $this->assertFalse($constraint->validate('blah'), 'The "blah" value is not part of the Enum');

        $this->assertFalse($constraint->validate(['foo']));
        $obj = new \stdClass();
        $obj->var1 = 'foo';
        $this->assertFalse($constraint->validate($obj));
    }

    public function testIsCheckingInvalidDataWithStrictType(): void
    {
        $constraint = new Enum(['01', '02', '03', '4', 5], null, true);
        $this->assertFalse($constraint->validate(2), 'The "2" value is not part of the Enum');
        $this->assertFalse($constraint->validate(4), 'The "01" value is not part of the Enum');
        $this->assertFalse($constraint->validate('5'), 'The "5" value is not part of the Enum');
    }

    public function testIsCheckingValidData(): void
    {
        $constraint = new Enum(['foo', 'bar', '08', 7]);
        $this->assertTrue($constraint->validate('foo'), 'The "foo" value should be part of the Enum');
        $this->assertTrue($constraint->validate('bar'), 'The "bar" value should be part of the Enum');
        $this->assertTrue($constraint->validate(8), 'The "8" value should be part of the Enum');
        $this->assertTrue($constraint->validate('07'), 'The "07" value should be part of the Enum');
    }

    public function testIsCheckingValidDataWithStrictType(): void
    {
        $constraint = new Enum(['01', '02', '03', '4'], null, true);
        $this->assertTrue($constraint->validate('02'), 'The "02" value should be part of the Enum');
        $this->assertTrue($constraint->validate('4'), 'The "4" value should be part of the Enum');
    }

    public function testIsGettingErrorMessage(): void
    {
        $constraint = new Enum(['foo', 'bar']);
        $this->assertFalse($constraint->validate('test'), 'The "test" value is not part of the Enum');
        $this->assertEquals('[field] Invalid option for enum. Allowed options are: foo, bar', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable(): void
    {
        $constraint = new Enum(['foo', 'bar'], 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

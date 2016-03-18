<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new Enum(['foo', 'bar']);
        $this->assertFalse($constraint->validate('test'), 'The "test" value is not part of the Enum');
        $this->assertFalse($constraint->validate('blah'), 'The "blah" value is not part of the Enum');
    }

    public function testIsCheckingValidData()
    {
        $constraint = new Enum(['foo', 'bar']);
        $this->assertTrue($constraint->validate('foo'), 'The "foo" value should be part of the Enum');
        $this->assertTrue($constraint->validate('bar'), 'The "bar" value should be part of the Enum');
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Enum(['foo', 'bar']);
        $this->assertFalse($constraint->validate('test'), 'The "test" value is not part of the Enum');
        $this->assertEquals('[field] Invalid option for enum. Allowed options are: foo, bar', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable()
    {
        $constraint = new Enum(['foo', 'bar'], 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

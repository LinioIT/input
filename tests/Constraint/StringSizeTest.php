<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use Linio\Component\Input\Constraint\StringSize;

class StringSizeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint1 = new StringSize(3);
        $this->assertFalse($constraint1->validate('ab'));

        $constraint2 = new StringSize(3, 5);
        $this->assertFalse($constraint2->validate('ab'));
        $this->assertFalse($constraint2->validate('abcdef'));

        $this->assertFalse($constraint1->validate(null));
        $this->assertFalse($constraint2->validate(null));
    }

    public function testIsCheckingValidData()
    {
        $constraint1 = new StringSize(3);
        $this->assertTrue($constraint1->validate('abc'));
        $this->assertTrue($constraint1->validate('abcdefghijklmnopqrstuvxywz'));

        $constraint2 = new StringSize(3, 5);
        $this->assertTrue($constraint2->validate('abc'));
        $this->assertTrue($constraint2->validate('abcd'));
        $this->assertTrue($constraint2->validate('abce'));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new StringSize(3);
        $this->assertFalse($constraint->validate('ab'));
        $this->assertEquals(sprintf('[field] Content out of min/max limit sizes [3, %s]', PHP_INT_MAX), $constraint->getErrorMessage('field'));

        $constraint = new StringSize(3, 5);
        $this->assertFalse($constraint->validate('ab'));
        $this->assertEquals('[field] Content out of min/max limit sizes [3, 5]', $constraint->getErrorMessage('field'));
    }

    public function testErrorMessageIsCustomizable()
    {
        $constraint = new StringSize(1, 2, 'CUSTOM!');
        $this->assertSame('[field] CUSTOM!', $constraint->getErrorMessage('field'));
    }
}

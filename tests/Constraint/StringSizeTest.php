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
        $this->assertEquals(sprintf('Content out of min/max limit sizes [3, %s]', PHP_INT_MAX), $constraint->getErrorMessage());

        $constraint = new StringSize(3, 5);
        $this->assertFalse($constraint->validate('ab'));
        $this->assertEquals('Content out of min/max limit sizes [3, 5]', $constraint->getErrorMessage());
    }
}

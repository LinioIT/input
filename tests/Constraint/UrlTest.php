<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new Url();
        $this->assertFalse($constraint->validate('http//foobar.com'));
        $this->assertFalse($constraint->validate('foobarcom'));
        $this->assertFalse($constraint->validate('foobar.com'));
        $this->assertFalse($constraint->validate('www.foobar.com'));
        $this->assertFalse($constraint->validate('www.foábar.com'));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new Url();
        $this->assertTrue($constraint->validate('http://foobar.com'));
        $this->assertTrue($constraint->validate('https://foobar.com'));
        $this->assertTrue($constraint->validate('ssh://foobar.com'));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Url();
        $this->assertFalse($constraint->validate('foobarcom'));
        $this->assertEquals('[field] Invalid URL format', $constraint->getErrorMessage('field'));
    }
}

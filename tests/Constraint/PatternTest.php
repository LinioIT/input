<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

use Linio\Component\Input\Constraint\Pattern;

class PatternTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingInvalidData()
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertFalse($constraint->validate(null));
        $this->assertFalse($constraint->validate(' 2014-04-22 '));
        $this->assertFalse($constraint->validate('2014-04-2'));
    }

    public function testIsCheckingValidData()
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertTrue($constraint->validate('2014-04-22'));
    }

    public function testIsGettingErrorMessage()
    {
        $constraint = new Pattern('/^\d{4}\-\d{2}-\d{2}$/');
        $this->assertFalse($constraint->validate(null));
        $this->assertEquals('Required pattern does not match', $constraint->getErrorMessage());
    }
}

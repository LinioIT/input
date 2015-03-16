<?php

namespace Linio\Component\Input;

use Linio\Component\Input\TypeHandler;
use Linio\Component\Input\Transformer\DateTimeTransformer;

class TypeHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCheckingType()
    {
        $typeHandler = new TypeHandler(['boolean' => 'is_bool']);

        $this->assertTrue($typeHandler->checkType('boolean', true));
        $this->assertTrue($typeHandler->checkType('ghost_type', 'test'));
    }

    public function testIsConvertingType()
    {
        $typeHandler = new TypeHandler([], ['datetime' => new DateTimeTransformer()]);

        $transformed = $typeHandler->convertType('datetime', '2014-01-01 00:00:01');
        $this->assertInstanceOf('\DateTime', $transformed);
        $this->assertEquals(new \DateTime('2014-01-01 00:00:01'), $transformed);
        $this->assertEquals('2014-01-01 00:00:01', $typeHandler->convertType('not_datetime', '2014-01-01 00:00:01'));
    }

    public function testIsConvertingTypeWithAddedTransformer()
    {
        $typeHandler = new TypeHandler();
        $typeHandler->addTypeTransformer('datetime', new DateTimeTransformer());

        $transformed = $typeHandler->convertType('datetime', '2014-01-01 00:00:01');
        $this->assertInstanceOf('\DateTime', $transformed);
        $this->assertEquals(new \DateTime('2014-01-01 00:00:01'), $transformed);
        $this->assertEquals('2014-01-01 00:00:01', $typeHandler->convertType('not_datetime', '2014-01-01 00:00:01'));
    }

    public function testIsAddingTypeCheck()
    {
        $typeHandler = new TypeHandler();
        $typeHandler->addTypeCheck('email', function ($value) {
            return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
        });

        $this->assertTrue($typeHandler->checkType('email', 'test@foobar.com'));
        $this->assertFalse($typeHandler->checkType('email', 'test'));
    }
}

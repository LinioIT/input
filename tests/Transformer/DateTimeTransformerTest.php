<?php
declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

use Linio\Component\Input\Transformer\DateTimeTransformer;

class DateTimeTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransformingIntoDateTime()
    {
        $transformer = new DateTimeTransformer();
        $transformed = $transformer->transform('2014-01-01 00:00:01');
        $this->assertInstanceOf('\DateTime', $transformed);
        $this->assertEquals(new \DateTime('2014-01-01 00:00:01'), $transformed);
    }

    public function testIsReturningNullWithInvalidDate()
    {
        $transformer = new DateTimeTransformer();
        $transformed = $transformer->transform('2014-01x01');
        $this->assertNull($transformed);
    }
}

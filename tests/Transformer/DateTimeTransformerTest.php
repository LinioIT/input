<?php

declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

use PHPUnit\Framework\TestCase;

class DateTimeTransformerTest extends TestCase
{
    public function testIsTransformingIntoDateTime(): void
    {
        $transformer = new DateTimeTransformer();
        $transformed = $transformer->transform('2014-01-01 00:00:01');
        $this->assertInstanceOf('\DateTime', $transformed);
        $this->assertEquals(new \DateTime('2014-01-01 00:00:01'), $transformed);
    }

    public function testIsReturningNullWithInvalidDate(): void
    {
        $transformer = new DateTimeTransformer();
        $transformed = $transformer->transform('2014-01x01');
        $this->assertNull($transformed);
    }

    public function testIsAllowingNullableValue(): void
    {
        $transformer = new DateTimeTransformer();
        $transformed = $transformer->transform(null);

        $this->assertNull($transformed);
    }
}

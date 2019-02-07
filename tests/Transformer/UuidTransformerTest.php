<?php

declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

use Linio\Component\Input\Exception\TransformationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidTransformerTest extends TestCase
{
    public function testItDoesTransformStringIntoUuid(): void
    {
        $transformer = new UuidTransformer();
        $transformed = $transformer->transform('d1d6228d-604c-4a8a-9396-42e6c3b17754');
        $this->assertInstanceOf(UuidInterface::class, $transformed);
        $this->assertEquals(Uuid::fromString('d1d6228d-604c-4a8a-9396-42e6c3b17754'), $transformed);
    }

    public function testItDoesThrowExceptionBecauseOfInvalidString(): void
    {
        $transformer = new UuidTransformer();

        $this->expectException(TransformationException::class);
        $transformer->transform('d1d6228d-604c');
    }
}

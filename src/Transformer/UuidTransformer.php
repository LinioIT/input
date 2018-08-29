<?php

declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

use Ramsey\Uuid\Uuid;

class UuidTransformer implements TransformerInterface
{
    public function transform($value)
    {
        return Uuid::fromString($value);
    }
}

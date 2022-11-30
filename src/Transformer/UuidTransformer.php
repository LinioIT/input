<?php

declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

use Linio\Component\Input\Exception\TransformationException;
use Ramsey\Uuid\Uuid;

class UuidTransformer implements TransformerInterface
{
    public function transform($value)
    {
        try {
            return Uuid::fromString($value);
        } catch (\Exception $exception) {
            throw new TransformationException($exception->getMessage());
        }
    }
}

<?php

declare(strict_types=1);

namespace Linio\Component\Input\Transformer;

interface TransformerInterface
{
    public function transform($value);
}

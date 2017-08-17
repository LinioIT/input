<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

class ConstructInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, array $data)
    {
        return new $class(...array_values($data));
    }
}

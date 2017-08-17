<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

interface InstantiatorInterface
{
    public function instantiate(string $class, array $data);
}

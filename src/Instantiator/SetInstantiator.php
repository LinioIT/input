<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Doctrine\Inflector\InflectorFactory;

class SetInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, array $data)
    {
        $inflector = InflectorFactory::create()->build();
        $object = new $class();

        foreach ($data as $key => $value) {
            $method = 'set' . $inflector->classify($key);
            $object->$method($value);
        }

        return $object;
    }
}

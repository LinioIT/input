<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Doctrine\Inflector\InflectorFactory;

class PropertyInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, ?array $data)
    {
        if ($data === null) {
            return null;
        }

        $inflector = InflectorFactory::create()->build();
        $object = new $class();

        foreach ($data as $key => $value) {
            $property = $inflector->camelize($key);
            $object->$property = $value;
        }

        return $object;
    }
}

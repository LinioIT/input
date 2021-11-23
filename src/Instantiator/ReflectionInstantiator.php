<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Doctrine\Inflector\InflectorFactory;

class ReflectionInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, array $data)
    {
        $inflector = InflectorFactory::create()->build();
        $object = new $class();
        $reflection = new \ReflectionClass($object);

        foreach ($data as $key => $value) {
            $property = $reflection->getProperty($inflector->camelize($key));
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $property->setValue($object, $value);
        }

        return $object;
    }
}

<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Doctrine\Common\Inflector\Inflector;

class ReflectionInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, array $data)
    {
        $object = new $class();
        $reflection = new \ReflectionClass($object);

        foreach ($data as $key => $value) {
            $property = $reflection->getProperty(Inflector::camelize($key));
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $property->setValue($object, $value);
        }

        return $object;
    }
}

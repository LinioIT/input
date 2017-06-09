<?php

declare(strict_types=1);

namespace Linio\Component\Input\Instantiator;

use Doctrine\Common\Inflector\Inflector;

class SetInstantiator implements InstantiatorInterface
{
    public function instantiate(string $class, array $data)
    {
        $object = new $class();

        foreach ($data as $key => $value) {
            $method = 'set' . Inflector::classify($key);
            $object->$method($value);
        }

        return $object;
    }
}

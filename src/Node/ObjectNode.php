<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Doctrine\Common\Inflector\Inflector;

class ObjectNode extends BaseNode
{
    public function getValue(string $field, $value)
    {
        $this->checkConstraints($field, $value);

        if ($this->hasDefault()) {
            $object = $this->default;
            foreach ($value as $key => $val) {
                $method = 'set' . Inflector::classify($key);
                $object->$method($val);
            }

            return $object;
        }

        return $this->instantiator->instantiate($this->type, $value);
    }
}

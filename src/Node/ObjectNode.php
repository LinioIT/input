<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

class ObjectNode extends BaseNode
{
    public function getValue(string $field, $value)
    {
        $this->checkConstraints($field, $value);

        return $this->instantiator->instantiate($this->type, $value);
    }
}

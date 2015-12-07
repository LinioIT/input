<?php
declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Instantiator\SetInstantiator;

class CollectionNode extends BaseNode
{
    public function getValue(string $field, $value)
    {
        $this->checkConstraints($field, $value);

        $items = [];

        foreach ($value as $collectionValue) {
            $items[] = $this->instantiator->instantiate($this->type, $collectionValue);
        }

        return $items;
    }

    public function hasChildren(): bool
    {
        return false;
    }
}

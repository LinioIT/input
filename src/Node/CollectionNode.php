<?php
declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Instantiator\SetInstantiator;

class CollectionNode extends BaseNode
{
    public function __construct()
    {
        $this->instantiator = new SetInstantiator();
    }

    public function getValue($value)
    {
        if (!$this->isRequired && !$value) {
            return $this->default;
        }

        $this->checkConstraints($value);

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

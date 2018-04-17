<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Exception\RequiredFieldException;
use Linio\Component\Input\Exception\InvalidConstraintException;

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

    public function walk($input)
    {
        $result = [];

        if (!$this->hasChildren()) {
            return $input;
        }

        foreach ($input as $inputItem) {
            if (! is_array($inputItem)) {
                throw new InvalidConstraintException(sprintf('Value does not match type collection'));
            }

            $itemResult = [];

            foreach ($this->getChildren() as $field => $config) {
                if (!array_key_exists($field, $inputItem)) {
                    if ($config->isRequired()) {
                        throw new RequiredFieldException($field);
                    }

                    if (!$config->hasDefault()) {
                        continue;
                    }

                    $inputItem[$field] = $config->getDefault();
                }

                $itemResult[$field] = $config->getValue($field, $config->walk($inputItem[$field]));
            }

            $result[] = $itemResult;
        }

        return $result;
    }
}

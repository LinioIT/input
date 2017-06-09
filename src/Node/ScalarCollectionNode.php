<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Exception\InvalidConstraintException;

class ScalarCollectionNode extends BaseNode
{
    public function getValue(string $field, $value)
    {
        $this->checkConstraints($field, $value);

        foreach ($value as $scalarValue) {
            if (!call_user_func('is_' . $this->type, $scalarValue)) {
                throw new InvalidConstraintException(sprintf('Value "%s" is not of type %s', $scalarValue, $this->type));
            }
        }

        return $value;
    }
}

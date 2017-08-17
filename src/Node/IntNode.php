<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\Type;

class IntNode extends BaseNode
{
    public function __construct()
    {
        $this->addConstraint(new Type('int'));
    }

    public function hasDefault(): bool
    {
        return is_int($this->default);
    }
}

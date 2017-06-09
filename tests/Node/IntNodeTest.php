<?php

namespace Linio\Component\Input\Node;

use PHPUnit\Framework\TestCase;

class IntNodeTest extends TestCase
{
    public function testIsReturningTrueForDefaultValueZero()
    {
        $node = new IntNode();
        $node->setDefault(0);

        $this->assertTrue($node->hasDefault());
    }
}

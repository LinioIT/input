<?php

namespace Linio\Component\Input\Node;

class IntNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsReturningTrueForDefaultValueZero()
    {
        $node = new IntNode();
        $node->setDefault(0);

        $this->assertTrue($node->hasDefault());
    }
}

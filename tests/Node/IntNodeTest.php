<?php

declare(strict_types=1);

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

    public function testIsReturningNullForDefaultValueNullWhenNullIsAllowed()
    {
        $node = new IntNode();
        $node->setAllowNull(true);
        $node->setDefault(null);

        $this->assertTrue($node->hasDefault());
    }

    public function testIsNotReturningNullForDefaultValueNullWhenNullIsNotAllowed()
    {
        $node = new IntNode();
        $node->setAllowNull(false);
        $node->setDefault(null);

        $this->assertFalse($node->hasDefault());
    }
}

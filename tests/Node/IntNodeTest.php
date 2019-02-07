<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use PHPUnit\Framework\TestCase;

class IntNodeTest extends TestCase
{
    public function testIsReturningTrueForDefaultValueZero(): void
    {
        $node = new IntNode();
        $node->setDefault(0);

        $this->assertTrue($node->hasDefault());
    }
}

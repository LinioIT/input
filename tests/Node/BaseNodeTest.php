<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\NotNull;
use Linio\Component\Input\Constraint\StringSize;
use Linio\Component\Input\Exception\InvalidConstraintException;
use Linio\Component\Input\Transformer\DateTimeTransformer;
use Linio\Component\Input\TypeHandler;
use PHPUnit\Framework\TestCase;

class BaseNodeTest extends TestCase
{
    public function testIsAddingChildNode()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string');

        $this->assertInstanceOf(BaseNode::class, $child);
        $this->assertCount(1, $base->getChildren());
    }

    public function testIsAddingRequiredChildNode()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['required' => false]);

        $this->assertInstanceOf(BaseNode::class, $child);
        $this->assertFalse($child->isRequired());
        $this->assertCount(1, $base->getChildren());
    }

    public function testIsNotOverridingNodeConstraints()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new class() extends StringNode {
            public function getConstraints(): array
            {
                return $this->constraints;
            }
        });

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['constraints' => [new NotNull()]]);

        $this->assertCount(2, $child->getConstraints());
    }

    public function testIsRemovingChildNode()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['required' => false]);
        $this->assertCount(1, $base->getChildren());

        $base->remove('foobar');
        $this->assertCount(0, $base->getChildren());
    }

    public function testIsDetectingChildsNode()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['required' => false]);

        $this->assertTrue($base->hasChildren());
    }

    public function testIsGettingValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string');
        $this->assertEquals('foobar', $child->getValue('foobar', 'foobar'));
    }

    public function testIsCheckingConstraintsOnValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['constraints' => [new StringSize(2, 5)]]);

        $this->expectException(InvalidConstraintException::class);
        $child->getValue('foobar', 'foobar');
    }

    public function testAllowingNullValuesIfConstraintsWouldOtherwiseReject()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['allow_null' => true, 'constraints' => [new NotNull()]]);

        $this->assertNull($child->getValue('foobar', null));
    }

    public function testIsGettingTransformedValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['transformer' => new DateTimeTransformer()]);
        $this->assertEquals(new \DateTime('2014-01-01 00:00:00'), $child->getValue('foobar', '2014-01-01 00:00:00'));
    }
}

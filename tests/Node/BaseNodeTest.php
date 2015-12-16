<?php
declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\TypeHandler;
use Linio\Component\Input\Constraint\StringSize;
use Linio\Component\Input\Transformer\DateTimeTransformer;

class BaseNodeTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException Linio\Component\Input\Exception\InvalidConstraintException
     */
    public function testIsCheckingConstraintsOnValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('string')->willReturn(new BaseNode());

        $base = new BaseNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'string', ['constraints' => [new StringSize(2, 5)]]);
        $child->getValue('foobar', 'foobar');
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

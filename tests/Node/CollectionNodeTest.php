<?php
declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\TypeHandler;
use Linio\Component\Input\Constraint\ConstraintInterface;

class CollectionNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsGettingValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn(new CollectionNode());

        $base = new CollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime');
        $child->setType('DateTime');
        $this->assertEquals([new \DateTime('@1389312000')], $child->getValue([['timestamp' => 1389312000]]));
    }

    public function testIsGettingDefaultValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn(new CollectionNode());

        $base = new CollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime', ['default' => 'foobar']);
        $this->assertEquals('foobar', $child->getValue(null));
    }

    /**
     * @expectedException Linio\Component\Input\Exception\InvalidConstraintException
     */
    public function testIsCheckingConstraintsOnValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn(new CollectionNode());

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->validate([['timestamp' => 1389312000]])->willReturn(false);
        $constraint->getErrorMessage()->shouldBeCalled();

        $base = new CollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime', ['constraints' => [$constraint->reveal()]]);
        $child->setType('DateTime');
        $child->getValue([['timestamp' => 1389312000]]);
    }
}

<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\ConstraintInterface;
use Linio\Component\Input\Instantiator\InstantiatorInterface;
use Linio\Component\Input\TypeHandler;

class CollectionNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsGettingValue()
    {
        $expectedInput = ['timestamp' => 1389312000];
        $expectedObj = new \DateTime('@1389312000');

        $instantiator = $this->prophesize(InstantiatorInterface::class);
        $instantiator->instantiate('DateTime', $expectedInput)->willReturn($expectedObj);

        $collectionNode = new CollectionNode();
        $collectionNode->setInstantiator($instantiator->reveal());

        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn($collectionNode);

        $base = new CollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime');
        $child->setType('DateTime');
        $this->assertEquals([$expectedObj], $child->getValue('foobar', [$expectedInput]));
    }

    /**
     * @expectedException \Linio\Component\Input\Exception\InvalidConstraintException
     */
    public function testIsCheckingConstraintsOnValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn(new CollectionNode());

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->validate([['timestamp' => 1389312000]])->willReturn(false);
        $constraint->getErrorMessage('foobar')->shouldBeCalled();

        $base = new CollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime', ['constraints' => [$constraint->reveal()]]);
        $child->setType('DateTime');
        $child->getValue('foobar', [['timestamp' => 1389312000]]);
    }
}

<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\ConstraintInterface;
use Linio\Component\Input\Exception\InvalidConstraintException;
use Linio\Component\Input\Instantiator\InstantiatorInterface;
use Linio\Component\Input\TypeHandler;
use PHPUnit\Framework\TestCase;

class ObjectNodeTest extends TestCase
{
    public function testIsGettingValue(): void
    {
        $expectedInput = ['timestamp' => 1389312000];
        $expectedObj = new \DateTime('@1389312000');

        $instantiator = $this->prophesize(InstantiatorInterface::class);
        $instantiator->instantiate('DateTime', $expectedInput)->willReturn($expectedObj);

        $objectNode = new ObjectNode();
        $objectNode->setInstantiator($instantiator->reveal());

        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn($objectNode);

        $base = new ObjectNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime');
        $child->setType('DateTime');
        $this->assertEquals($expectedObj, $child->getValue('foobar', $expectedInput));
    }

    public function testIsCheckingConstraintsOnValue(): void
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('DateTime')->willReturn(new ObjectNode());

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->validate(['timestamp' => 1389312000])->willReturn(false);
        $constraint->getErrorMessage('foobar')->shouldBeCalled();

        $base = new ObjectNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'DateTime', ['constraints' => [$constraint->reveal()]]);
        $child->setType('DateTime');

        $this->expectException(InvalidConstraintException::class);
        $child->getValue('foobar', ['timestamp' => 1389312000]);
    }
}

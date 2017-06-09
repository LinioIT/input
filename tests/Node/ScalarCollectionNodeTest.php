<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\ConstraintInterface;
use Linio\Component\Input\TypeHandler;

class ScalarCollectionNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsGettingValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('int')->willReturn(new ScalarCollectionNode());

        $base = new ScalarCollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'int');
        $child->setType('int');
        $this->assertEquals([15, 25, 36], $child->getValue('foobar', [15, 25, 36]));
    }

    /**
     * @expectedException \Linio\Component\Input\Exception\InvalidConstraintException
     * @expectedExceptionMessage Value "25" is not of type int
     */
    public function testIsDetectingBadTypes()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('int')->willReturn(new ScalarCollectionNode());

        $base = new ScalarCollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'int');
        $child->setType('int');
        $child->getValue('foobar', [15, '25']);
    }

    /**
     * @expectedException \Linio\Component\Input\Exception\InvalidConstraintException
     */
    public function testIsCheckingConstraintsOnValue()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('int')->willReturn(new ScalarCollectionNode());

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->validate([15, 25, 36])->willReturn(false);
        $constraint->getErrorMessage('foobar')->shouldBeCalled();

        $base = new ScalarCollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'int', ['constraints' => [$constraint->reveal()]]);
        $child->setType('int');
        $child->getValue('foobar', [15, 25, 36]);
    }
}

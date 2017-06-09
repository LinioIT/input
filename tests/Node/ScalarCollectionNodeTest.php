<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\ConstraintInterface;
use Linio\Component\Input\Exception\InvalidConstraintException;
use Linio\Component\Input\TypeHandler;
use PHPUnit\Framework\TestCase;

class ScalarCollectionNodeTest extends TestCase
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

    public function testIsDetectingBadTypes()
    {
        $typeHandler = $this->prophesize(TypeHandler::class);
        $typeHandler->getType('int')->willReturn(new ScalarCollectionNode());

        $base = new ScalarCollectionNode();
        $base->setTypeHandler($typeHandler->reveal());
        $child = $base->add('foobar', 'int');
        $child->setType('int');

        $this->expectException(InvalidConstraintException::class);
        $this->expectExceptionMessage('Value "25" is not of type int');
        $child->getValue('foobar', [15, '25']);
    }

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

        $this->expectException(InvalidConstraintException::class);
        $child->getValue('foobar', [15, 25, 36]);
    }
}

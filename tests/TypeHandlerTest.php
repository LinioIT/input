<?php
declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Node\BaseNode;
use Linio\Component\Input\Node\ObjectNode;
use Linio\Component\Input\Node\DateTimeNode;
use Linio\Component\Input\Node\ScalarCollectionNode;
use Linio\Component\Input\Node\CollectionNode;

class TypeHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAddingTypes()
    {
        $typeHandler = new TypeHandler();
        $typeHandler->addType('foobar', BaseNode::class);
        $type = $typeHandler->getType('foobar');
        $this->assertInstanceOf(BaseNode::class, $type);
    }

    public function testIsCreatingScalarCollections()
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('int[]');
        $this->assertInstanceOf(ScalarCollectionNode::class, $type);
    }

    public function testIsCreatingCollections()
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('DateTime[]');
        $this->assertInstanceOf(CollectionNode::class, $type);
    }

    public function testIsCreatingObjects()
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('DateTime');
        $this->assertInstanceOf(ObjectNode::class, $type);
    }

    public function testIsCreatingInputHandlers()
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('Linio\Component\Input\InputHandler');
        $this->assertInstanceOf(BaseNode::class, $type);
    }

    public function testIsDetectingConflictWithCaseInsensitive()
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('datetime');
        $this->assertInstanceOf(DateTimeNode::class, $type);
    }
}

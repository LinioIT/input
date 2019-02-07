<?php

declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Node\BaseNode;
use Linio\Component\Input\Node\CollectionNode;
use Linio\Component\Input\Node\DateTimeNode;
use Linio\Component\Input\Node\ObjectNode;
use Linio\Component\Input\Node\ScalarCollectionNode;
use PHPUnit\Framework\TestCase;

class TypeHandlerTest extends TestCase
{
    public function testIsAddingTypes(): void
    {
        $typeHandler = new TypeHandler();
        $typeHandler->addType('foobar', BaseNode::class);
        $type = $typeHandler->getType('foobar');
        $this->assertInstanceOf(BaseNode::class, $type);
    }

    public function testIsCreatingScalarCollections(): void
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('int[]');
        $this->assertInstanceOf(ScalarCollectionNode::class, $type);
    }

    public function testIsCreatingCollections(): void
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('DateTime[]');
        $this->assertInstanceOf(CollectionNode::class, $type);
    }

    public function objectProvider()
    {
        return [
            ['DateTime'],
            [\DateTimeInterface::class],
        ];
    }

    /**
     * @dataProvider objectProvider
     */
    public function testIsCreatingObjects($className): void
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType($className);
        $this->assertInstanceOf(ObjectNode::class, $type);
    }

    public function testIsDetectingConflictWithCaseInsensitive(): void
    {
        $typeHandler = new TypeHandler();
        $type = $typeHandler->getType('datetime');
        $this->assertInstanceOf(DateTimeNode::class, $type);
    }
}

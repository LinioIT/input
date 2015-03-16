<?php

namespace Linio\Component\Input\Handler;

use Linio\Component\Input\Handler\FieldNode;

class FieldNodeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCreatingNodes()
    {
        $node = new FieldNode();
        $node->add('test');
        $node->add('foo', 'boolean');
        $node->add('bar', 'datetime', ['required' => false]);

        $this->assertInstanceOf('Linio\Component\Input\Handler\FieldNode', $node['test']);
        $this->assertEquals('string', $node['test']->getType());
        $this->assertTrue($node['test']->isRequired());

        $this->assertInstanceOf('Linio\Component\Input\Handler\FieldNode', $node['foo']);
        $this->assertEquals('boolean', $node['foo']->getType());
        $this->assertTrue($node['foo']->isRequired());

        $this->assertInstanceOf('Linio\Component\Input\Handler\FieldNode', $node['bar']);
        $this->assertEquals('datetime', $node['bar']->getType());
        $this->assertFalse($node['bar']->isRequired());
    }

    public function testIsRemovingNodes()
    {
        $node = new FieldNode();
        $node->add('test');
        $node->remove('test');
        $this->assertCount(0, $node);
    }

    public function testIsDetectingArray()
    {
        $node = new FieldNode();
        $node->setType('array');
        $this->assertTrue($node->hasChildren(), 'Arrays have children');
    }

    public function testIsDetectingMixed()
    {
        $node = new FieldNode();
        $node->setType('mixed');
        $this->assertTrue($node->isMixed());
    }

    public function testIsDetectingObject()
    {
        $node = new FieldNode();
        $node->setType('ArrayObject');
        $this->assertTrue($node->isObject(), 'ArrayObject is an object');

        $node->setType('FooBar');
        $this->assertFalse($node->isObject(), 'FooBar is not an object');
    }

    public function testIsGettingCollectionType()
    {
        $node = new FieldNode();
        $node->setType('ArrayObject[]');
        $this->assertEquals('ArrayObject', $node->getCollectionType());

        $node->setType('FooBar[');
        $this->assertFalse($node->getCollectionType(), 'Foobar[ should no be a collection');
    }

    public function testIsDetectingCollection()
    {
        $node = new FieldNode();
        $node->setType('ArrayObject[]');
        $this->assertTrue($node->isCollection(), 'ArrayObject[] should be a collection');

        $node->setType('FooBar[');
        $this->assertFalse($node->isCollection(), 'Foobar[ should no be a collection');
    }

    public function testIsDetectingScalarCollection()
    {
        $node = new FieldNode();
        $node->setType('int[]');
        $this->assertTrue($node->isScalarCollection(), 'int[] should be a collection');

        $node->setType('FooBar[');
        $this->assertFalse($node->isScalarCollection(), 'Foobar[ should no be a collection');
    }
}

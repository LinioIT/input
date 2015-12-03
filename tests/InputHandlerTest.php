<?php
declare(strict_types=1);

namespace Linio\Component\Input;

class TestUser
{
    public $name;
    public $age;
    public $date;
    public $isActive;
    public $related;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    public function setRelated(TestUser $related)
    {
        $this->related = $related;
    }
}

class TestInputHandler extends InputHandler
{
    public function define()
    {
        $this->add('title', 'string');
        $this->add('size', 'int');
        $this->add('dimensions', 'int[]');
        $this->add('date', 'datetime');
        $this->add('metadata', 'array');

        $simple = $this->add('simple', 'array');
        $simple->add('title', 'string');
        $simple->add('size', 'int', ['required' => false, 'default' => 15]);
        $simple->add('date', 'datetime');

        $author = $this->add('author', 'Linio\Component\Input\TestUser');
        $author->add('name', 'string');
        $author->add('age', 'int');
        $related = $author->add('related', 'Linio\Component\Input\TestUser');
        $related->add('name', 'string');
        $related->add('age', 'int');

        $fans = $this->add('fans', 'Linio\Component\Input\TestUser[]');
        $fans->add('name', 'string');
        $fans->add('age', 'int');
    }
}

class InputHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsHandlingBasicInput()
    {
        $input = [
            'title' => 'Foobar',
            'size' => 35,
            'dimensions' => [11, 22, 33],
            'date' => '2015-01-01 22:50',
            'metadata' => [
                'foo' => 'bar',
            ],
            'simple' => [
                'title' => 'Barfoo',
                'date' => '2015-01-01 22:50',
            ],
            'author' => [
                'name' => 'Barfoo',
                'age' => 28,
                'related' => [
                    'name' => 'Barfoo',
                    'age' => 28,
                ],
            ],
            'fans' => [
                [
                    'name' => 'A',
                    'age' => 18,
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                ]
            ],
        ];

        $inputHandler = new TestInputHandler();
        $inputHandler->bind($input);
        $this->assertTrue($inputHandler->isValid());

        // Basic fields
        $this->assertEquals('Foobar', $inputHandler->getData('title'));
        $this->assertEquals(35, $inputHandler->getData('size'));

        // Scalar collection
        $this->assertEquals([11, 22, 33], $inputHandler->getData('dimensions'));

        // Transformer
        $this->assertEquals(new \DateTime('2015-01-01 22:50'), $inputHandler->getData('date'));

        // Mixed array
        $this->assertEquals(['foo' => 'bar'], $inputHandler->getData('metadata'));

        // Typed array
        $this->assertEquals(['title' => 'Barfoo', 'size' => 15, 'date' => new \DateTime('2015-01-01 22:50')], $inputHandler->getData('simple'));

        // Object and nested object
        $related = new TestUser();
        $related->setName('Barfoo');
        $related->setAge(28);
        $author = new TestUser();
        $author->setName('Barfoo');
        $author->setAge(28);
        $author->setRelated($related);
        $this->assertEquals($author, $inputHandler->getData('author'));

        // Object collection
        $fanA = new TestUser();
        $fanA->setName('A');
        $fanA->setAge(18);
        $fanB = new TestUser();
        $fanB->setName('B');
        $fanB->setAge(28);
        $fanC = new TestUser();
        $fanC->setName('C');
        $fanC->setAge(38);
        $this->assertEquals([$fanA, $fanB, $fanC], $inputHandler->getData('fans'));
    }

    public function testIsHandlingErrors()
    {
        $input = [
            'title' => 'Foobar',
            'size' => '35',
            'dimensions' => ['11', 22, 33],
            'date' => '2015-01-01 22:50',
            'metadata' => [
                'foo' => 'bar',
            ],
            'simple' => [
                'date' => '2015-01-01 22:50',
            ],
            'author' => [
                'name' => 'Barfoo',
                'age' => 28,
                'related' => [
                    'name' => 'Barfoo',
                    'age' => 28,
                ],
            ],
            'fans' => [
                [
                    'name' => 'A',
                    'age' => 18,
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                ]
            ],
        ];

        $inputHandler = new TestInputHandler();
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
        $this->assertEquals([
            '[size] Value does not match type: int',
            '[dimensions] Value "11" is not of type int',
            '[title] Missing required field: title',
        ], $inputHandler->getErrors());
        $this->assertEquals('[size] Value does not match type: int, [dimensions] Value "11" is not of type int, [title] Missing required field: title', $inputHandler->getErrorsAsString());
    }
}

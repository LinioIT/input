<?php

declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Constraint\Range;
use Linio\Component\Input\Instantiator\InstantiatorInterface;
use Linio\Component\Input\Instantiator\PropertyInstantiator;
use PHPUnit\Framework\TestCase;

class TestUser
{
    protected $name;
    protected $age;
    protected $date;
    protected $related;
    public $isActive;
    public $birthday;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function getRelated(): TestUser
    {
        return $this->related;
    }

    public function setRelated(TestUser $related)
    {
        $this->related = $related;
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
    }

    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = $birthday;
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
        $simple->add('title', 'string', ['default' => 'Barfoo']);
        $simple->add('size', 'int', ['required' => false, 'default' => 15]);
        $simple->add('date', 'datetime');

        $author = $this->add('author', 'Linio\Component\Input\TestUser');
        $author->add('name', 'string');
        $author->add('age', 'int');
        $author->add('is_active', 'bool', ['required' => false]);
        $related = $author->add('related', 'Linio\Component\Input\TestUser');
        $related->add('name', 'string');
        $related->add('age', 'int');

        $fans = $this->add('fans', 'Linio\Component\Input\TestUser[]');
        $fans->add('name', 'string');
        $fans->add('age', 'int');
        $fans->add('birthday', 'datetime');
    }
}

class TestRecursiveInputHandler extends InputHandler
{
    public function define()
    {
        $this->add('title', 'string');
        $this->add('size', 'int');
        $this->add('child', \stdClass::class, ['handler' => new TestInputHandler(), 'instantiator' => new PropertyInstantiator()]);
    }
}

class DummyUser
{
    protected $id;

    protected $name;

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }
}

class TestDefaultsInputHandler extends InputHandler
{
    public function define()
    {
        $user = $this->add('user', DummyUser::class);
        $user->add('name', 'string');
    }
}

class InputHandlerTest extends TestCase
{
    public function testIsHandlingDefaults()
    {
        $input = [
            'user' => [
                'name' => 'Foo',
            ],
        ];

        $user = new DummyUser();
        $user->setId(1);
        $user->setName('Bar');

        $inputHandler = new TestDefaultsInputHandler();
        $inputHandler->bind($input, ['user' => $user]);

        // Basic fields
        $this->assertEquals('Foo', $inputHandler->getData('user')->getName());
        $this->assertEquals(1, $inputHandler->getData('user')->getId());
    }

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
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                    'birthday' => '2000-01-02',
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                    'birthday' => '2000-01-03',
                ],
            ],
        ];

        $inputHandler = new TestInputHandler();
        $inputHandler->bind($input);
        $this->assertTrue($inputHandler->isValid());

        // Basic fields
        $this->assertTrue($inputHandler->hasData('title'));
        $this->assertFalse($inputHandler->hasData('...'));
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
        $fanA->setBirthday(new \DateTime('2000-01-01'));
        $fanB = new TestUser();
        $fanB->setName('B');
        $fanB->setAge(28);
        $fanB->setBirthday(new \DateTime('2000-01-02'));
        $fanC = new TestUser();
        $fanC->setName('C');
        $fanC->setAge(38);
        $fanC->setBirthday(new \DateTime('2000-01-03'));
        $this->assertEquals([$fanA, $fanB, $fanC], $inputHandler->getData('fans'));
    }

    public function testIsHandlingErrors()
    {
        $input = [
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
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                    'birthday' => '2000-01-01',
                ],
            ],
        ];

        $inputHandler = new TestInputHandler();
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
        $this->assertEquals([
            'Missing required field: title',
        ], $inputHandler->getErrors());
        $this->assertEquals('Missing required field: title', $inputHandler->getErrorsAsString());
    }

    public function testIsHandlingTypeJuggling()
    {
        $input = [
            'title' => '',
            'size' => 0,
            'dimensions' => [0, 0, 0],
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
                'is_active' => false,
                'related' => [
                    'name' => 'Barfoo',
                    'age' => 28,
                ],
            ],
            'fans' => [
                [
                    'name' => 'A',
                    'age' => 18,
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                    'birthday' => '2000-01-01',
                ],
            ],
        ];

        $inputHandler = new TestInputHandler();
        $inputHandler->bind($input);
        $this->assertTrue($inputHandler->isValid());

        $this->assertEquals('', $inputHandler->getData('title'));
        $this->assertEquals(0, $inputHandler->getData('size'));
        $this->assertEquals([0, 0, 0], $inputHandler->getData('dimensions'));
        $this->assertFalse($inputHandler->getData('author')->isActive);
    }

    public function testIsHandlingInputValidationWithInstantiator()
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
                'date' => '2015-01-01 22:50',
            ],
            'user' => [
                'name' => false,
                'age' => '28',
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
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'B',
                    'age' => 28,
                    'birthday' => '2000-01-01',
                ],
                [
                    'name' => 'C',
                    'age' => 38,
                    'birthday' => '2000-01-01',
                ],
            ],
        ];

        $instantiator = $this->prophesize(InstantiatorInterface::class);
        $instantiator->instantiate('Linio\Component\Input\TestUser', [])->shouldNotBeCalled();

        $inputHandler = new TestInputHandler();
        $user = $inputHandler->add('user', 'Linio\Component\Input\TestUser', ['instantiator' => $instantiator->reveal()]);
        $user->add('name', 'string');
        $user->add('age', 'int');
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
        $this->assertEquals([
            '[name] Value does not match type: string',
        ], $inputHandler->getErrors());
    }

    public function testIsHandlingInputWithRecursiveHandler()
    {
        $input = [
            'title' => 'Barfoo',
            'size' => 20,
            'child' => [
                'title' => 'Foobar',
                'size' => 35,
                'dimensions' => [11, 22, 33],
                'date' => '2015-01-01 22:50',
                'metadata' => [
                    'foo' => 'bar',
                ],
                'simple' => [
                    'date' => '2015-01-01 22:50',
                ],
                'user' => [
                    'name' => false,
                    'age' => '28',
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
                        'birthday' => '2000-01-01',
                    ],
                    [
                        'name' => 'B',
                        'age' => 28,
                        'birthday' => '2000-01-02',
                    ],
                    [
                        'name' => 'C',
                        'age' => 38,
                        'birthday' => '2000-01-03',
                    ],
                ],
            ],
        ];

        $inputHandler = new TestRecursiveInputHandler();
        $inputHandler->bind($input);
        $this->assertTrue($inputHandler->isValid());

        // Basic fields
        $this->assertEquals('Barfoo', $inputHandler->getData('title'));
        $this->assertEquals(20, $inputHandler->getData('size'));
        /** @var \stdClass $child */
        $child = $inputHandler->getData('child');

        // Scalar collection
        $this->assertEquals([11, 22, 33], $child->dimensions);

        // Transformer
        $this->assertEquals(new \DateTime('2015-01-01 22:50'), $child->date);

        // Mixed array
        $this->assertEquals(['foo' => 'bar'], $child->metadata);

        // Typed array
        $this->assertEquals(['title' => 'Barfoo', 'size' => 15, 'date' => new \DateTime('2015-01-01 22:50')], $child->simple);

        // Object and nested object
        $related = new TestUser();
        $related->setName('Barfoo');
        $related->setAge(28);
        $author = new TestUser();
        $author->setName('Barfoo');
        $author->setAge(28);
        $author->setRelated($related);
        $this->assertEquals($author, $child->author);

        // Object collection
        $fanA = new TestUser();
        $fanA->setName('A');
        $fanA->setAge(18);
        $fanA->setBirthday(new \DateTime('2000-01-01'));
        $fanB = new TestUser();
        $fanB->setName('B');
        $fanB->setAge(28);
        $fanB->setBirthday(new \DateTime('2000-01-02'));
        $fanC = new TestUser();
        $fanC->setName('C');
        $fanC->setAge(38);
        $fanC->setBirthday(new \DateTime('2000-01-03'));
        $this->assertEquals([$fanA, $fanB, $fanC], $child->fans);
    }

    public function testOverride()
    {
        $input = [
            'price' => 'igor',
        ];

        $inputHandler = new TestConstraintOverrideType();
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
    }

    public function invalidDateProvider(): \Generator
    {
        yield [''];

        yield ['Invalid%20date'];

        yield [123];

        yield [false];

        yield [true];

        yield [[]];

        yield [null];
    }

    /**
     * @dataProvider invalidDateProvider
     *
     * @param mixed $datetime
     */
    public function testDatetimeInvalidDatetimeInput($datetime)
    {
        $input = [
            'date' => $datetime,
        ];

        $inputHandler = new TestDatetimeNotValidatingDate();
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
    }
}

class TestConstraintOverrideType extends InputHandler
{
    public function define()
    {
        $this->add('price', 'int', [
            'required' => true,
            'constraints' => [new Range(0)],
        ]);
    }
}

class TestDatetimeNotValidatingDate extends InputHandler
{
    public function define()
    {
        $this->add('date', 'datetime', [
            'required' => true,
        ]);
    }
}

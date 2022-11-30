<?php

declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Constraint\Email;
use Linio\Component\Input\Constraint\Enum;
use Linio\Component\Input\Constraint\Range;
use Linio\Component\Input\Constraint\StringSize;
use Linio\Component\Input\Instantiator\InstantiatorInterface;
use Linio\Component\Input\Instantiator\PropertyInstantiator;

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

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age): void
    {
        $this->age = $age;
    }

    public function getRelated(): TestUser
    {
        return $this->related;
    }

    public function setRelated(TestUser $related): void
    {
        $this->related = $related;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday;
    }
}

class TestInputHandler extends InputHandler
{
    public function define(): void
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
    public function define(): void
    {
        $this->add('title', 'string');
        $this->add('size', 'int');
        $this->add('child', \stdClass::class, ['handler' => new TestInputHandler(), 'instantiator' => new PropertyInstantiator()]);
    }
}

class TestRecursiveInputHandlerExplicit extends InputHandler
{
    public function define(): void
    {
        $this->add('title', 'string');
        $this->add('size', 'int');
        $this->add('child', \stdClass::class, ['instantiator' => new PropertyInstantiator()], new TestInputHandler());
    }
}

class TestNullableInputHandler extends InputHandler
{
    public function define(): void
    {
        $this->add('name', 'string');
        $this->add('address', 'string', ['allow_null' => true]);
    }
}

class TestNullableRecursiveInputHandler extends InputHandler
{
    public function define(): void
    {
        $this->add('type', 'string');
        $this->add('data', \stdClass::class, [
            'handler' => new TestNullableInputHandler(),
            'instantiator' => new PropertyInstantiator(),
            'allow_null' => true,
        ]);
    }
}

class TestInputHandlerCascade extends InputHandler
{
    public function define(): void
    {
        $this->add('name', 'string')
            ->setRequired(true)
            ->addConstraint(new StringSize(1, 80));

        $this->add('age', 'int')
            ->setRequired(true)
            ->addConstraint(new Range(1, 99));

        $this->add('gender', 'string')
            ->setRequired(true)
            ->addConstraint(new Enum(['male', 'female', 'other']));

        $this->add('birthday', 'datetime')
            ->setRequired(false);

        $this->add('email', 'string')
            ->setRequired(false)
            ->addConstraint(new Email());
    }
}

class InputHandlerTest extends TestCase
{
    public function testIsHandlingBasicInput(): void
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

    public function testIsHandlingErrors(): void
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

    public function testIsHandlingTypeJuggling(): void
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

    public function testIsHandlingInputValidationWithInstantiator(): void
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

    public function testIsHandlingInputWithRecursiveHandler(): void
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

    public function testIsHandlingInputWithRecursiveHandlerExplicit(): void
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

        $inputHandler = new TestRecursiveInputHandlerExplicit();
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

    public function testOverride(): void
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
     */
    public function testDatetimeInvalidDatetimeInput($datetime): void
    {
        $input = [
            'date' => $datetime,
        ];

        $inputHandler = new TestDatetimeNotValidatingDate();
        $inputHandler->bind($input);
        $this->assertFalse($inputHandler->isValid());
    }

    public function testIsHandlingInputWithNullValues(): void
    {
        $input = [
            'type' => 'buyers',
            'data' => [
                'name' => 'John Doe',
                'address' => null,
            ],
        ];

        $inputHandler = new TestNullableRecursiveInputHandler();
        $inputHandler->bind($input);

        $this->assertTrue($inputHandler->isValid());

        $data = $inputHandler->getData('data');

        $this->assertNull($data->address);

        $input = [
            'type' => 'buyers',
            'data' => null,
        ];

        $inputHandler = new TestNullableRecursiveInputHandler();
        $inputHandler->bind($input);

        $this->assertTrue($inputHandler->isValid());

        $data = $inputHandler->getData('data');

        $this->assertNull($data);
    }

    public function testInputHandlerOnCascade(): void
    {
        $input = [
            'name' => 'A',
            'age' => 18,
            'gender' => 'male',
            'birthday' => '2000-01-01',
        ];

        $inputHandler = new TestInputHandlerCascade();
        $inputHandler->bind($input);

        $this->assertTrue($inputHandler->isValid());
    }
}

class TestConstraintOverrideType extends InputHandler
{
    public function define(): void
    {
        $this->add('price', 'int', [
            'required' => true,
            'constraints' => [new Range(0)],
        ]);
    }
}

class TestDatetimeNotValidatingDate extends InputHandler
{
    public function define(): void
    {
        $this->add('date', 'datetime', [
            'required' => true,
        ]);
    }
}

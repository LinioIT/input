<?php

declare(strict_types=1);

namespace Linio\Component\Input;

use PHPUnit\Framework\TestCase;

class SchemaTestInputHandler extends InputHandler
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

        $authors = $this->add('authors', 'Linio\Component\Input\TestUser[]');
        $authors->add('name', 'string');
    }
}

class SchemaBuilderTest extends TestCase
{
    public function testIsBuildingSchema(): void
    {
        $expectedSchema = [
            'title' => [
                'type' => 'string',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [],
            ],
            'size' => [
                'type' => 'int',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [],
            ],
            'dimensions' => [
                'type' => 'int[]',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [],
            ],
            'date' => [
                'type' => 'datetime',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [],
            ],
            'metadata' => [
                'type' => 'array',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [],
            ],
            'simple' => [
                'type' => 'array',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [
                    'title' => [
                        'type' => 'string',
                        'required' => false,
                        'default' => 'Barfoo',
                        'nullable' => false,
                        'children' => [],
                    ],
                    'size' => [
                        'type' => 'int',
                        'required' => false,
                        'default' => 15,
                        'nullable' => false,
                        'children' => [],
                    ],
                    'date' => [
                        'type' => 'datetime',
                        'required' => true,
                        'default' => null,
                        'nullable' => false,
                        'children' => [],
                    ],
                ],
            ],
            'author' => [
                'type' => 'object',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [
                    'name' => [
                        'type' => 'string',
                        'required' => true,
                        'default' => null,
                        'nullable' => false,
                        'children' => [],
                    ],
                    'age' => [
                        'type' => 'int',
                        'required' => true,
                        'default' => null,
                        'nullable' => false,
                        'children' => [],
                    ],
                    'is_active' => [
                        'type' => 'bool',
                        'required' => false,
                        'default' => null,
                        'nullable' => false,
                        'children' => [],
                    ],
                ],
            ],
            'authors' => [
                'type' => 'object[]',
                'required' => true,
                'default' => null,
                'nullable' => false,
                'children' => [
                    'name' => [
                        'type' => 'string',
                        'required' => true,
                        'default' => null,
                        'nullable' => false,
                        'children' => [],
                    ],
                ],
            ],
        ];

        $schemaBuilder = new SchemaBuilder();
        $schema = $schemaBuilder->build(new SchemaTestInputHandler());

        $this->assertEquals($expectedSchema, $schema);
    }
}

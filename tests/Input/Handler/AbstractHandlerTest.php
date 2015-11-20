<?php

namespace Linio\Component\Input\Handler;

use Symfony\Component\HttpFoundation\Request;
use Linio\Component\Input\Handler\AbstractHandler;
use Linio\Component\Input\Constraint\Pattern;

class ExampleHandler extends AbstractHandler
{
    public function define()
    {
        $this->add('name');
        $this->add('date', 'datetime', ['required' => false]);

        // Typed array
        $array = $this->add('fixed_stuff', 'array');
        $array->add('foo');

        // Scalar array
        $this->add('integers', 'int[]');

        // Mixed array
        $this->add('stuff', 'mixed');

        // Entity mapping
        $stationAlias = $this->add('station_alias', 'Linio\Component\Input\Handler\StationMock');
        $stationAlias->add('station_id', 'int');
        $stationAlias->add('locale', 'string', ['required' => false, 'constraints' => [new Pattern('/^[a-z]{2}_[A-Z]{2}$/')]]);
        $stationAlias->add('name');
        $stationAlias->add('is_primary', 'boolean');

        // Child object
        $point = $stationAlias->add('geopoint', 'Linio\Component\Input\Handler\Point');
        $point->add('latitude', 'float');
        $point->add('longitude', 'float');

        // Collection
        $stationMocks = $stationAlias->add('station_mocks', 'Linio\Component\Input\Handler\StationMock[]');
        $stationMocks->add('name');
    }
}

class Point
{
    protected $latitude;
    protected $longitude;

    /**
     * Empty constructor that requires an argument. This is to help validate
     * that the AbstractHandler is able to hydrate objects with required
     * constructor arguments.
     *
     * @param $foo
     */
    public function __construct($foo)
    {
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}

class StationMock
{
    protected $stationId;
    protected $locale;
    protected $name;
    protected $isPrimary;
    protected $geopoint;
    protected $stationMocks;

    public function getStationId()
    {
        return $this->stationId;
    }

    public function setStationId($stationId)
    {
        $this->stationId = $stationId;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }

    public function getGeopoint()
    {
        return $this->geopoint;
    }

    public function setGeopoint($geopoint)
    {
        $this->geopoint = $geopoint;
    }

    public function addStationMock($stationMock)
    {
        $this->stationMocks[] = $stationMock;
    }

    public function getStationMocks()
    {
        return $this->stationMocks;
    }
}

class AbstractRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testIsBindingRequest()
    {
        $request = $this->getValidRequest();
        $input = $this->getInputHandler();
        $input->bind($request);
        $this->assertTrue($input->isValid(), 'The request object is not valid: ' . $input->getErrorsAsString());

        $data = $input->getData();
        $this->assertEquals('Herval', $data['name']);
        $this->assertInstanceOf('DateTime', $data['date']);
        $this->assertEquals('2014-01-01 00:00:01', $data['date']->format('Y-d-m H:i:s'));

        // Fixed array data
        $this->assertEquals('bar', $data['fixed_stuff']['foo']);

        // Scalar array data
        $this->assertEquals([1, 2, 3], $data['integers']);

        // Mixed array data
        $this->assertEquals('bar', $data['stuff']['foo']);

        // Entity validation
        $this->assertInstanceOf('Linio\Component\Input\Handler\StationMock', $data['station_alias']);
        $this->assertEquals(1, $data['station_alias']->getStationId());
        $this->assertEquals('pt_BR', $data['station_alias']->getLocale());
        $this->assertEquals('Rodoviária de Herval', $data['station_alias']->getName());
        $this->assertInstanceOf('Linio\Component\Input\Handler\Point', $data['station_alias']->getGeopoint());
        $this->assertEquals(-32.024482, $data['station_alias']->getGeopoint()->getLatitude());
        $this->assertEquals(-53.394903, $data['station_alias']->getGeopoint()->getLongitude());

        // Collection
        $stationMocks = $data['station_alias']->getStationMocks();
        $this->assertInstanceOf('Linio\Component\Input\Handler\StationMock', $stationMocks[0]);
        $this->assertEquals('Hervalino', $stationMocks[0]->getName());
        $this->assertInstanceOf('Linio\Component\Input\Handler\StationMock', $stationMocks[1]);
        $this->assertEquals('Juvenal', $stationMocks[1]->getName());
    }

    public function testIsGettingSpecificDataByKey()
    {
        $request = $this->getValidRequest();
        $input = $this->getInputHandler();
        $input->bind($request);
        $data = $input->getData();
        $this->assertEquals('Herval', $input->getData('name'));
    }

    public function testIsValidatingParameters()
    {
        $request = new Request([], [
            'name' => null,
            'stuff' => [
                'foo' => 'bar',
            ],
            'integers' => [1, 2, 'abc'],
            'fixed_stuff' => [
                'foo' => 'bar',
            ],
            'station_alias' => [
                'station_id' => 1,
                'locale' => 'pt_BR',
                'name' => 'Rodoviária de Herval',
                'is_primary' => 'true',
                'geopoint' => [
                    'latitude' => -32.024482,
                    'longitude' => -53.394903,
                ],
                'station_mocks' => [
                    [
                        'name' => 'Hervalino'
                    ]
                ],
            ]
        ]);

        $input = $this->getInputHandler();
        $input->bind($request);
        $this->assertFalse($input->isValid());

        $errors = $input->getErrors();
        $this->assertCount(3, $errors);
        $this->assertEquals('"name" is required', $errors[0]);
        $this->assertEquals('Value "abc", from "integers", is not of type int', $errors[1]);
        $this->assertEquals('"is_primary" should be of type boolean, string received.', $errors[2]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsThrowingExceptionWhenGettingInvalidData()
    {
        $request = new Request([], [
            'name' => null,
            'stuff' => [
                'foo' => 'bar',
            ],
        ]);

        $input = $this->getInputHandler();
        $input->bind($request);
        $input->getData();
    }

    public function testIsCheckingConstraints()
    {
        $request = new Request([], [
            'name' => null,
            'stuff' => [
                'foo' => 'bar',
            ],
            'integers' => [1, 2, 3],
            'fixed_stuff' => [
                'foo' => 'bar',
            ],
            'date' => '2014-01-01 00:00:01',
            'station_alias' => [
                'station_id' => 1,
                'locale' => 'pt-BR',
                'name' => 'Rodoviária de Herval',
                'is_primary' => 'true',
                'geopoint' => [
                    'latitude' => -32.024482,
                    'longitude' => -53.394903,
                ],
                'station_mocks' => [
                    [
                        'name' => 'Hervalino'
                    ]
                ],
            ]
        ]);

        $input = $this->getInputHandler();
        $input->bind($request);
        $this->assertFalse($input->isValid());

        $errors = $input->getErrors();
        $this->assertCount(3, $errors);
        $this->assertEquals('"name" is required', $errors[0]);
        $this->assertEquals('"locale" constraint: Required pattern does not match', $errors[1]);
        $this->assertEquals('"is_primary" should be of type boolean, string received.', $errors[2]);
    }

    protected function getValidRequest()
    {
        return new Request([], [
            'name' => 'Herval',
            'date' => '2014-01-01 00:00:01',
            'stuff' => [
                'foo' => 'bar',
            ],
            'integers' => [1, 2, 3],
            'fixed_stuff' => [
                'foo' => 'bar',
            ],
            'station_alias' => [
                'station_id' => 1,
                'locale' => 'pt_BR',
                'name' => 'Rodoviária de Herval',
                'is_primary' => true,
                'geopoint' => [
                    'latitude' => -32.024482,
                    'longitude' => -53.394903,
                ],
                'station_mocks' => [
                    [
                        'name' => 'Hervalino',
                    ],
                    [
                        'name' => 'Juvenal',
                    ]
                ],
            ]
        ]);
    }

    protected function getInputHandler()
    {
        $typeChecks = [
            'boolean' => 'is_bool',
            'float' => 'is_float',
            'double' => 'is_float',
            'int' => 'is_int',
            'integer' => 'is_int',
            'numeric' => 'is_numeric',
            'string' => 'is_string',
        ];

        $typeTransformers = [
            'datetime' => new \Linio\Component\Input\Transformer\DateTimeTransformer(),
        ];

        $typeHandler = new \Linio\Component\Input\TypeHandler($typeChecks, $typeTransformers);
        $inputHandler = new ExampleHandler($typeHandler);

        return $inputHandler;
    }
}

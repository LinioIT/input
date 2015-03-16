<?php

namespace Linio\Component\Input;

use Linio\Component\Input\Factory;
use Linio\Component\Input\Handler\AbstractHandler;

class ExampleHandler extends AbstractHandler
{
    public function define()
    {
        $this->add('name');
    }
}

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCreatingHandlers()
    {
        $typeHandlerMock = $this->getMockBuilder('Linio\Component\Input\TypeHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $inputFactory = new Factory();
        $inputFactory->setHandlerNamespace('Linio\Component\Input');
        $inputFactory->setTypeHandler($typeHandlerMock);

        $inputHandler = $inputFactory->getHandler('example');
        $this->assertInstanceOf('\Linio\Component\Input\ExampleHandler', $inputHandler);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testIsDetectingNonExistingHandlerClass()
    {
        $typeHandlerMock = $this->getMockBuilder('Linio\Component\Input\TypeHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $inputFactory = new Factory();
        $inputFactory->setHandlerNamespace('This\Does\Not\Exists');
        $inputFactory->setTypeHandler($typeHandlerMock);

        $inputHandler = $inputFactory->getHandler('simple');
    }
}

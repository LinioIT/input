<?php

namespace Linio\Component\Input\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Linio\Component\Input\TypeHandler;
use Linio\Component\Input\Factory;

class InputServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['input.type_handler'] = function() {
            $typeHandler = new \Linio\Component\Input\TypeHandler();

            return $typeHandler;
        };

        $container['input.factory'] = function ($container) {
            $inputFactory = new \Linio\Component\Input\Factory();
            $inputFactory->setHandlerNamespace($container['input.handler_namespace']);
            $inputFactory->setTypeHandler($container['input.type_handler']);

            return $inputFactory;
        };
    }
}

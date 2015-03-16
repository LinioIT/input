Linio Input
===========
[![Latest Stable Version](https://poser.pugx.org/linio/input/v/stable.svg)](https://packagist.org/packages/linio/input) [![License](https://poser.pugx.org/linio/input/license.svg)](https://packagist.org/packages/linio/input) [![Build Status](https://secure.travis-ci.org/LinioIT/input.png)](http://travis-ci.org/LinioIT/input) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LinioIT/input/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LinioIT/input/?branch=master)

Linio Input is yet another component of the Linio Framework. It aims to
abstract HTTP request input handling, allowing a seamless integration with
your domain model. The component is responsible for:

* Parsing request body contents
* Validating input data
* Hydrating input data into objects

Install
-------

The recommended way to install Linio Input is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "linio/input": "dev-master"
    }
}
```

Tests
-----

To run the test suite, you need install the dependencies via composer, then
run PHPUnit.

    $ composer install
    $ phpunit

Usage
-----

The library is very easy to use: first, you have to register the services. For
Silex, a service provider is included. Just register it:

```php
<?php

$app->register(new \Linio\Component\Input\Provider\InputServiceProvider(), [
    'input.handler_namespace' => 'Linio\Api\Handler',
]);
```

Note that must provide a handler namespace for the service, this is where all your
input handlers will be located. The input handlers are responsible for specifying
which data you're expecting to receive from requests. Let's create one:

```php
<?php

namespace Linio\Api\Handler;

use Linio\Component\Input\Handler\AbstractHandler;

class RegistrationHandler extends AbstractHandler
{
    public function define()
    {
        $this->add('referrer', 'string');
        $this->add('registration_date', 'datetime');

        $user = $this->add('user', 'Linio\Model\User');
        $user->add('name', 'string');
        $user->add('email', 'string');
        $user->add('age', 'integer');
    }
}
```

Now, in your controller, you need to use the `InputFactory` service to
use your input handler and treat the `Request`.

```php
<?php

namespace Linio\Api\Controller;

use Linio\Component\Input\InputTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController
{
    use InputTrait;

    /**
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $input = $this->getInputHandler('order');
        $input->bind($request);

        if (!$input->isValid()) {
            return new Response($input->getErrors());
        }

        $data = $input->getData();
        $data['referrer']; // string
        $data['registration_date']; // \DateTime
        $data['user']; // Linio\Model\User

        return new Response(['message' => 'Valid!']);
    }
}
```

The `InputTrait` is included in the library. It allows you to easily inject
the library as a dependency in your controllers, or other classes, and also
provides some helper methods, like the `getInputHandler()`, responsible for
loading the appropriate input handler by alias. The alias, in this case, is
the string `registration`, which will be converted to a FQCN by the factory,
like `Linio\Api\Handler\RegistrationHandler`.

Constraints
-----------

Linio Input allows you to apply constraints to your fields. This can be done
by providing a third argument for the `add()` method in your input handlers:


```php
<?php

class RegistrationHandler extends AbstractHandler
{
    public function define()
    {
        $this->add('referrer', 'string', ['required' => true]);
        $this->add('registration_date', 'datetime');

        $user = $this->add('user', 'Linio\Model\User');
        $user->add('name', 'string');
        $user->add('email', 'string', ['constraints' => [new Pattern('/^\S+@\S+\.\S+$/')]]);
        $user->add('age', 'integer');
    }
}
```

The library includes several constraints by default:

* Enum
* GuidValue
* NotNull
* Pattern
* StringSize

Transformers
------------

Linio Input allows you to create data transformers, responsible for converting
simple input data, like timestamps and unique IDs, into something meaningful,
like a datetime object or the full entity (by performing a query).

```php
<?php

namespace Linio\Api\Handler\Transformer;

use Doctrine\Common\Persistence\ObjectRepository;
use Linio\Component\Input\Transformer\TransformerInterface;

class IdTransformer implements TransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        try {
            $entity = $this->repository->find($value);
        } catch (\Exception $e) {
            return null;
        }

        return $entity;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository $repository
     */
    public function setRepository(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }
}

```

Data transformers are treated as datatypes inside your handlers and must be
registered to the Linio Input `TypeHandler`. They are also services, with
dependencies. So, they should look like this in your container:

```php
<?php

$app['input.transformer.user_id'] = function($app) {
    $transformer = new \Linio\Api\Handler\Transformer\IdTransformer();
    $transformer->setRepository($app['orm.em']->getRepository('Linio\Api\Repository\User'));

    return $transformer;
};

$app['input.type_handler']->addTypeTransformer('user_id', $app['input.transformer.user_id']);
```

And now you just have to use it like a type in your handlers:

```php
<?php

class RegistrationHandler extends AbstractHandler
{
    public function define()
    {
        $this->add('user', 'user_id');
    }
}
```

Type Checks
-----------

You might have noticed that Linio Input includes basic type validation by
default. The types that are available by default are:

* boolean
* float
* double
* int
* integer
* numeric
* string
* datetime

All types, except for `datetime` which is a default transformer, are checked
via callables. You can add new type checks easily by calling the `addTypeCheck()`
method on the Linio Input `TypeHandler`.

```php
<?php

$app['input.type_handler']->addTypeCheck('email', function ($value) {
    return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
});
```

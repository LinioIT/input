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

The library is very easy to use: first, you have to create your input
handler class. The input handlers are responsible for specifying
which data you're expecting to receive from requests. Let's create one:

```php
<?php

namespace Linio\Api\Handler;

use Linio\Component\Input\InputHandler;

class RegistrationHandler extends InputHandler
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

Now, in your controller, you just need to bind data to the handler:

```php
<?php

namespace Linio\Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController
{
    public function registerAction(Request $request): Response
    {
        $input = new RegistrationHandler();
        $input->bind($request->request->all());

        if (!$input->isValid()) {
            return new Response($input->getErrorsAsString());
        }

        $data = $input->getData();
        $data['referrer']; // string
        $data['registration_date']; // \DateTime
        $data['user']; // Linio\Model\User

        return new Response(['message' => 'Valid!']);
    }
}
```

Type Handler
------------

When you are defining the fields for your input handler, there are a few types
available: string, int, bool, datetime, etc. Those are predefined types
provided by the library, but you can also create your own. This magic is
handled by `Linio\Component\Input\TypeHandler`. The `TypeHandler` allows you to
add new types, which are extensions of the `BaseNode` class.

```php
<?php

class GuidNode extends BaseNode
{
    public function __construct()
    {
        $this->addConstraint(new Linio\Component\Input\Constraint\GuidValue());
    }
}

$typeHandler = new Linio\Component\Input\TypeHandler();
$typeHandler->addType('guid', GuidNode::class);

$input = new RegistrationHandler();
$input->setTypeHandler($typeHandler);

```

In this example, we have created a new `guid` type, which has a built-in constraint
to validate contents. You can use custom types to do all sorts of things: add
predefined constraint chains, transformers, instantiators and also customize how
values are generated.


Constraints
-----------

Linio Input allows you to apply constraints to your fields. This can be done
by providing a third argument for the `add()` method in your input handlers:


```php
<?php

use Linio\Component\Input\Constraint\Pattern;

class RegistrationHandler extends InputHandler
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
     * @var ObjectRepository
     */
    protected $repository;

    public function transform($value)
    {
        try {
            $entity = $this->repository->find($value);
        } catch (\Exception $e) {
            return null;
        }

        return $entity;
    }

    public function setRepository(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }
}

```

Data transformers can be added on a per-field basis during the definition
of your input handler:

```php
<?php

use Linio\Api\Handler\Transformer\IdTransformer;

class RegistrationHandler extends InputHandler
{
    /**
     * @var IdTransformer
     */
    protected $idTransformer;

    public function define()
    {
        $this->add('store_id', 'string', ['transformer' => $this->idTransformer]);
    }

    public function setIdTransformer(IdTransformer $idTransformer)
    {
        $this->idTransformer = $idTransformer;
    }
}
```

Instantiators
-------------

Linio Input allows you to use different object instantiators on a per-field
basis. This can be done by providing a third argument for the `add()` method
in your input handlers:


```php
<?php

use Linio\Component\Input\Instantiator\ConstructInstantiator;
use Linio\Component\Input\Instantiator\ReflectionInstantiator;

class RegistrationHandler extends InputHandler
{
    public function define()
    {
        $this->add('foobar', 'My\Foo\Class', ['instantiator' => new ConstructInstantiator()]);
        $this->add('barfoo', 'My\Bar\Class', ['instantiator' => new ReflectionInstantiator()]);
    }
}
```

The library includes several instantiators by default:

* ConstructInstantiator
* PropertyInstantiator
* SetInstantiator
* ReflectionInstantiator

By default, the `SetInstantiator` is used by Object and Collection nodes.

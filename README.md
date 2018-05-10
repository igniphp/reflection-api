# ![Igni logo](https://github.com/igniphp/common/blob/master/logo/full.svg)
[![Build Status](https://travis-ci.org/igniphp/reflection-api.svg?branch=master)](https://travis-ci.org/igniphp/reflection-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/igniphp/reflection-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/igniphp/reflection-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/igniphp/reflection-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/igniphp/reflection-api/?branch=master)

## Installation
```
composer require igniphp/reflection-api
```

## Reflection API
Licensed under MIT License.

Reflection api provides tools that allows to:
 - read and write object's properties
 - build classes on runtime
 - retrieves closure's body
 - instantiating objects without reflection api

### Reading object's properties

```php
use Igni\Utils\ReflectionApi;

class TestClass
{
    private $test;
    
    public function __construct()
    {
        $this->test = 1;
    }
}

$instance = new TestClass();

ReflectionApi::readProperty($instance, 'test');
```

### Write object's properties

```php
use Igni\Utils\ReflectionApi;

class TestClass
{
    private $test;
    
    public function __construct()
    {
        $this->test = 1;
    }
}

$instance = new TestClass();

ReflectionApi::writeProperty($instance, 'test', 2);
```

### Create an instance

```php
use Igni\Utils\ReflectionApi;

class TestClass
{
    private $test;
    
    public function __construct()
    {
        $this->test = 1;
    }
}

$instance = ReflectionApi::createInstance(TestClass::class);
```

### Building and loading class on runtime

```php
use Igni\Utils\ReflectionApi;
use Igni\Utils\ReflectionApi\RuntimeProperty;
use Igni\Utils\ReflectionApi\RuntimeMethod;

$class = ReflectionApi::createClass('TestClass');

$class->addProperty((new RuntimeProperty('test))->makePrivate());

$constructor = new RuntimeMethod('__construct');
$constructor->addBody(
    '$this->test = 1'
);

$getTest = new RuntimeMethod('getTest');
$getTest->setReturnType('string');
$getTest->addBody(
    'return $this->test'
);

$class->addMethod($constructor);
$class->addMethod($getTest);

$class->load();

$instance = $class->createInstance();

$instance instanceof 'TestClass';// true.
$instance->getTest();// 1

```

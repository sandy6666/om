# OM (ObjectManager)
### The Advanced Object Manager with dependency injection, plugins, preferences, and much more... NO MORE new Obj()!

No more cluttering your project with unnecessary object creation. Now inject whatever object you want in your class in the constructor, and let OM take care of it. It's that simple!

This project was heavily inspired by [Magento 2](https://github.com/magento/magento2 "Magento 2")'s use of Object Manager, and dependency injection.

## Installation
`composer require jayankaghosh/om`

## Features

OM (ObjectManager) helps developers organize and maintain their code easily with powerful features like 
- Dependency Injection
- Constructor parameter injection (Magento-like)
- Plugins (Maento-like,)
- Preferences, (Magento-like)
- Parameter injection (Magento-like)

## How To Use

#### 1. Simple usage

    // Get object manager instance from object manager factory Like this
    $objectManagerFactory = new \Om\OmFactory();
    $objectManager = $objectManagerFactory->getInstance();
    
    // And then create the initial object of your project 
    // using $objectManager instead of "new"
    // $objectManager::get method takes 2 parameters
    // 1. the class name as string
    // 2. an associative array of constructor arguments of the class
    $app = $objectManager->get(\My\Project\App::class, []);
    $app->run();
    
    // After that we are open to a new world of possibilities 
    // that will change the way programming was done forever!!! 
    // Too much? Okay too much 😐
    
### 2. Constructor arguments

Using OM we don't have to worry about constructor arguments. 
If the argument type is an object, it'll automatically be passed (as a singleton)
to the constructor

    class A {...}
    class B {...}
    class C {
        public function __construct(A $a, B $b) {...}
        ...
    }
    
    // $c = new C(); // Will throw error. Since C required A and B to run
    $c = $objectManager->get(C::class); // Works like a charm!
    
### 3. The DI config

Remember when we created an object of OmFactory?

    $objectManagerFactory = new \Om\OmFactory();
    
Well it turns out we can pass a config object to it as well as a parameter.
This config holds information about how the ObjectManager should handle
the dependency injection. We can pass configurations regarding 
__preferences__, __type arguments__, and __plugins__ using the config object.

The config is an object of `Om\DiConfig\Config`. We can either use the functions
provided inside the class to make the config. Or we can use an associative 
array to make it. In this example, we'll use the latter

    $config = \Om\DiConfig\Config::fromArray([
        'preferences' => [
            [
                'for' => 'MyCalculator',
                'type' => Calculator::class
            ]
        ],
        'types' => [
            [
                'name' => Calculator::class,
                'arguments' => [
                    [
                        'name' => 'logger',
                        'type' => 'object',
                        'value' => \Monolog\Logger::class
                    ]
                ],
                'plugins' => [
                    [
                        'name' => 'divide-by-zero-check',
                        'type' => CalculatorPlugin::class
                    ]
                ]
            ]
        ]
    ]);
    $objectManagerFactory = new \Om\OmFactory($config);
    $objectManager = $objectManagerFactory->getInstance();
    
You can also create the __DI Config__ from a XML string using the 
`\Om\DiConfig\Config::fromXml` method, like this,

    $config = Om\DiConfig\Config::fromXml(
        \file_get_contents(__DIR__  . '/di.xml')
    );
    
Here is a sample of how the `di.xml` file should look like

    <?xml version="1.0" encoding="utf-8" ?>
    <config>
        <preference for="MyCalculator" type="Calculator" />
        <type name="Calculator">
            <arguments>
                <argument name="logger" xsi:type="object">Monolog\Logger</argument>
            </arguments>
            <plugin name="divide-by-zero-check" type="CalculatorPlugin" />
        </type>
    </config>
    

### 4. Preferences

Preferences are a way of making aliases or proxies for objects. Keeping 'B' as a
 preference to 'A' is like saying "_Whenever I want to create an object of class 'A', please instead 
create an object of class 'B' instead_"

Preferences are defined using the DI Config

    $config = \Om\DiConfig\Config::fromArray([
        'preferences' => [
            [
                'for' => 'MyCalculatorAlias',
                'type' => Calculator::class
            ]
        ]
    ]);
    $objectManagerFactory = new \Om\OmFactory($config);
    $objectManager = $objectManagerFactory->getInstance();
    
    // There is not class with the name 'MyCalculatorAlias',
    // but still it doesn't throw any errors because we had already
    // mentioned that Calculator::class is the preference for 'MyCalculatorAlias'
    $calculator = $objectManager->get('MyCalculatorAlias');
    echo get_class($calculator); // Calculator::class

### 5. Constructor arguments

Let's say we have a class A which has 2 constructor arguments

    class A {
        public function __constructor(
            Logger $logger,
            string $message
        ) {
            var_dump($message);
        }
    }
    
Now if we want to create an object of class A using object manager

    $objectManager->get('A');
    
It will throw an error saying too few arguments passed. But why? I thought
 OM took care of all constructor arguments :(
 
Well, not exactly. __OM can only pass parameters which have type as object__.
If the type is something else (in this case $message is a __string__).
Hence we would need to pass the argument "message" ourselves. How?
 
    $a = $objectManager->get('A', ['message' => 'Hello OM']);
    
By passing it as a part of an associative array in the second parameter of `get`.
We can pass as many constructor arguments as we want like this. We can even
pass arguments of type object, and it'll override the default argument.

But isn't it frustrating having to pass the value of `message` every time
we want to create an object of A? Hence there is also another way of 
passing global arguments to classes. That is through the __DI Config__

    $config = \Om\DiConfig\Config::fromArray([
        'types' => [
            [
                'name' => 'A',
                'arguments' => [
                    [
                        'name' => 'message',
                        'type' => 'string',
                        'value' => 'Hello Global OM'
                    ]
                ]
            ]
        ]
    ]);
    $objectManagerFactory = new \Om\OmFactory($config);
    $objectManager = $objectManagerFactory->getInstance();
    
    $objectManager->get('A'); // doesn't throw error anymore
    
### 6. Singletons

By default all objects created using the `\Om\ObjectManager\ObjectManager::get`
methods are singletons. Which means, if an object of the class is already created, it'll
return that same object, or create a new one if it's the first time.

There might be some times when we don't want this behaviour and want a new Object
to be created. In cases like these, we use the `\Om\ObjectManager\ObjectManager::create` method.

    $aSingleton = $objectManager->get('A'); // singleton
    $aNew = $objectManager->create('A'); // new object
    
### 7. Generated classes

In order to provide certain supercalifragilisticexpialidocious features,
OM might want to generate a few files in your system (not a worm. pinky swear!).
For that we need to provide the absolute path of a directory where OM can 
generate the files.

    $config = [...];
    $writableDirectoryPath = __DIR__ . '/var/generated/';
    $objectManagerFactory = new \Om\OmFactory($config, $writableDirectoryPath);
    $objectManager = $objectManagerFactory->getInstance();

### 8. Factory Pattern

Requires [Generated Classes](#7-generated-classes)

Once we gave OM the writable directory to generate files in, we can use the
factory pattern of OM to generate new classes instead of singleton.
For that simply suffix the word `Factory` after the class name.

    class A {
        public function __construct(
            \My\Useful\HelperClass $helper,
            \My\Useful\HelperClassFactory $helperFactory
        ) {
            $this->helper = $helper; // singleton
            $this->helper = $helperFactory->create(); // new object
        }
    }
    
### 9. Plugins

Requires [Generated Classes](#7-generated-classes)

Plugins are an elegant way of achieving complete extendability of an
application. Plugins give us the ability to intercept the flow of __any 
public method__ to do something __before__, __after__, or __around__ the method
execution.

Plugins are also defined in the __DI Config__

    $config = \Om\DiConfig\Config::fromArray([
        'types' => [
            [
                'name' => 'Calculator',
                'plugins' => [
                    [
                        'name' => 'divide-by-zero-check',
                        'type' => 'CalculatorPlugin'
                    ]
                ]
            ]
        ]
    ]);
    $objectManagerFactory = new \Om\OmFactory($config);
    $objectManager = $objectManagerFactory->getInstance();
    
    class Calculator {
        public function divide($a, $b)
        {
            return $a / $b;    
        }
    }
    
    
    class CalculatorPlugin {
        public function beforeDivide(Calculator $subject, $a, $b)
        {
            if ($b === 0) {
                $b = 1;
                echo '$b changed to 1 because it was 0';
            }
            return [$a, $b];
        }
    }
    
    $calculator = $objectManager->get('Calculator');
    
As you can see, we modified the behaviour of the `divide` method in the
`Calculator` class without even touching the Calculator class. How cool
is that?!

Here's a handy guide for plugins

#### Before
 
Used to modify parameters. Prefix `before` to the method name 
in the plugin. For example, `beforeDivide`.

Arguments

1. Subject (an object of the class it is a plugin for)
2. Parameter 1 of the actual method
3. Parameter 2 of the actual method
4. ...

#### After

Used to modify the return value. Prefix `after` to the method name
in the plugin. For example, `afterDivide`.

Arguments

1. Subject (an object of the class it is a plugin for)
2. The return value of the actual function

#### Around

Used to change the entire flow of the method. Here the method itself 
is not executed automatically. Rather it is converted into a callable and
passed to the plugin as an argument. Prefix `around` to the method name
in the plugin. For example, `aroundDivide`.

Arguments

1. Subject (an object of the class it is a plugin for)
2. callable (a callable method pointing to the actual method)
3. Parameter 1 of the actual method
4. Parameter 2 of the actual method
5. ...

### Examples can be found in the /examples directory to help you get started
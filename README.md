## CakePimpleDi - Cake 3 Plugin
A cakephp plugin for dependency injection based on Pimple library

## Requirements
* CakePHP 3.X

# Installation
---------
To install this plugin you'll only have to add the plugin to your required section of composer.json then load in your bootstrap file.

```
composer require require rochamarcelo/cake-pimple-di
```

# Bootstrap
---------

Load the plugin as any other plugin:

```php
Plugin::load('RochaMarcelo/CakePimpleDi', ['bootstrap' => true, 'routes' => false]);
```

The bootstrap file must be loaded, to set up all configurations needed

## Register dependencies

In your configuration file you can define all the services:

```php
<?php
return [
    ...

    'CakePimpleDi' => [
        'services' => [
            'LibraryApp\Client' => function() {//each time you get that service, will returns the same instance
                return new \Cake\Network\Http\Client;
            },
            'LibraryApp\Finder' => function($c) {//each time you get that service, will returns  the same instance
                $finder = new \LibraryApp\Finder\SimpleFinder(
                	$c['LibraryApp\Client']
                );

                return $finder;
            },
            'random_func' => [
                'value' => function () {
                    return rand();
                },
                'type' => 'parameter'//when you get that service, will return the original closure
            ],
            'cookie_name' => 'SESSION_ID',
            [
                'id' => 'something',
                'value' => function () {
                    $std = new \stdClass;
                    $std->rand = rand();
                    return $std;
                },
                'type' => 'factory'//will return  a different instance for all calls
            ]
        ]
    ]
];
```

## Loading dependency
Get the shared instance then call method "get"

```php
use RochaMarcelo\CakePimpleDi\Di\Di;
$finder = Di::instance()->get('LibraryApp\Finder');
```

## Using the DiTrait

```php

namespace App\Controller;

use RochaMarcelo\CakePimpleDi\Di\DiTrait;

class BooksController extends AppController
{
	use DiTrait;

    public function index()
    {
        $finder = $this->di()->get('LibraryApp\Finder');
    }
}
```

## What is Pimple?

Pimple is a simple PHP Dependency Injection Container, to more information visit: http://pimple.sensiolabs.org


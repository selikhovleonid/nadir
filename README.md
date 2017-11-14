# Nadir

Yet Another PHP Microframework.

[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)]()

Nadir is a PHP microframework which helps you quickly write web applications, console 
applications and RESTful services. It's based on the MVC meta-pattern. This microframework 
provides wide opportunities for modification and customization.

## Installing

You will need Composer dependency manager to install Nadir. The easiest way 
to start working with Nadir is to create project skeleton running the following 
command:

```
composer create-project -s dev selikhovleonid/nadir-skeleton <project-name>
```

## Project structure

The created project template will have a structure simular to this:

<pre>
├── cli
│   └── cli.php
├── config
│   └── main.php
├── controllers
│   ├── Cli.php
│   ├── System.php
│   └── Test.php
├── extensions
│   └── core
│       ├── AbstractAuth.php
│       ├── AbstractModel.php
│       ├── Auth.php
│       ├── Process.php
│       └── SystemCtrlInterface.php
├── models
│   └── Test.php
├── vendor
├── views
│   ├── layouts
│   │   └── main.php
│   ├── snippets
│   │   └── topbar.php
│   └── views
│       ├── system
│       │   ├── page401.php
│       │   ├── page403.php
│       │   └── page404.php
│       └── test
│           └── default.php
└── web
    └── index.php
</pre>

## Main configuration file

The configuration of any application (console or web) is contained in the file 
`/config/main.php`. This is an associative array that is read when the microframework
core is loaded. The array is available for modification and extension and includes 
the following elements by default:

```php
array(
    // The path map of the application components
    'componentsRootMap' => array(
        'models'      => '/models',
        'controllers' => '/controllers',
        'views'       => '/views/views',
        'layouts'     => '/views/layouts',
        'snippets'    => '/views/snippets',
        'images'      => '/web/assets/imgs',
        'js'          => '/web/js',
        'css'         => '/web/css'
    ),
    // The default name of the layout
    'defaultLayout'     => 'main',
    // The routing table that contains the correspondence between the request URN
    // and the Controller-Action pair
    'routeMap'          => array(
        'cli'    => array(
            '--test' => array(
                'ctrl' => array('Cli', 'actionTest'),
            ),
        ),
        'get'    => array(
            '/'  => array(
                'ctrl' => array('Test', 'actionDefault'),
                'auth' => false,
            ),
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => false,
            ),
        ),
        'post'   => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => false,
            ),
        ),
        'put'    => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => false,
            ),
        ),
        'delete' => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => false,
            ),
        ),
    ),
);
```

Access to the configurations within the client code is done by calling the 
`\nadir\core\AppHelper::getInstance()->getConfig('configName')` method.
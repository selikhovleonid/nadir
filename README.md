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

```php
\nadir\core\AppHelper::getInstance()->getConfig('configName')
```

## Controller

The controller is an instance of a class inherited from the `\nadir\core\AbstractWebController` 
or the `\nadir\core\AbstractCliController` abstract superclasses. 
When called, the controller performs some action, which usually refers to the model 
for the purpose of obtaining data, their further conversion and transfer to the 
view.

### Controller and view

In the life cycle of the web application, after the query binding with the Controller-Action 
pair, a controller object is created, which by default tries to associate the view 
objects with it. The view is generally composite and consists of a Layout (an 
instance of the class `\nadir\core\Layout`) and View in a narrow sense (object of 
the class `\nadir\core\View`). Defaulted view objects are assigned only if there 
are associated markup files. The name of the markup file is obtained by discarding 
the 'action' prefix from the action name, the file is placed in the directory with 
the controller name (file names and directories should be in lowercase). View objects 
are available within the controller by calling the accessors `$this->getView()`, 
`$this->setView()`, `$this->getLayout()`, and `$this->setLayout()`. At any time 
prior to the beginning of page rendering, it is possible to change the default Layout 
or View to any other available single-type object.

Passing values of user variables from the controller to the view:

```php
namespace controllers;

use nadir\core\AbstractWebCtrl;

class Test extends AbstractWebCtrl
{

    public function actionDefault()
    {
        // ...
        $this->getView()->foo  = 'foo';
        $this->getView()->bar  = 'bar';
        $this->getView()->setVariables(array(
            'baz'  => 'baz',
            'qux'  => 'qux',
            'quux' => 'quux',
        ));
        // ...
    }
}
```
In the markup file `/views/views/test/default.php` of this view the variables are 
readable by calling `$this->foo`, `$this->bar`, `$this->baz` and so on.
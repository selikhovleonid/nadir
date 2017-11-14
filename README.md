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
php composer.phar create-project -s dev selikhovleonid/nadir-skeleton <project-name>
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
for the purpose of obtaining data, their further conversion and pass to the view.

### Controller and view

In the lifetime of the web application, after the query binding with the Controller-Action 
pair, a controller object is created, which by default tries to associate the view 
objects with it. The view is generally composite and consists of a Layout (an 
instance of the class `\nadir\core\Layout`) and View in a narrow sense (object of 
the class `\nadir\core\View`). Defaulted view objects are assigned only if there 
are associated markup files. The name of the markup file is obtained by discarding 
the 'action' prefix from the action name, the file is placed in the directory with 
the controller name (file names and directories should be in lowercase). View objects 
are available within the controller by calling the accessors `$this->getView()`, 
`$this->setView()`, `$this->getLayout()` and `$this->setLayout()`. At any time 
prior to the beginning of page rendering, it's possible to change the default Layout 
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
        $this->getView()->bar  = array(42, 'bar');
        $this->getView()->setVariables(array(
            'baz'  => 'baz',
            'qux'  => 'qux',
            'quux' => 'quux',
        ));
        // ...
        $this->render();
    }
}
```
In the markup file `/views/views/test/default.php` of this view the variables are 
readable by calling `$this->foo`, `$this->bar`, `$this->baz` and so on. The page 
is rendered by calling `$this->render()` within the action. You can render a page 
containing only the View file (it's clear that Layout in this case must be null). 
Moreover, in case of AJAX-request HTML-page rendering is often not needed at all, 
a more specific answer format is required, in this case the `\nadir\core\AbstractWebCtrl::renderJson()`
method is provided.

### CLI controller

The example of shell command:

```
php cli.php --test --foo=bar
```

This command after the query binding according route table will be processed by 
the CLI controller action:

```php
namespace controllers;

use nadir\core\AbstractCliCtrl;

class Cli extends AbstractCliCtrl
{

    public function actionTest(array $aArgs)
    {
        if (!empty($aArgs)) {
            $this->printInfo('The test cli action was called with args: '
                .implode(', ', $aArgs).'.');
        } else {
            $this->printError(new \Exception('The test cli action was called without args.'));
        }
    }
}
```

## View

### Composite view

The view contains HTML-code and a minimum of logic, which is necessary only for 
operating variables received from the controller. The view is generally composite 
and consists of a Layout (an instance of the class `\nadir\core\Layout`) and View 
in a narrow sense (object of the class `\nadir\core\View`). Each of the composites 
of the view can in turn contain snippets (objects of the `\nadir\core\Snippet` class) - 
fragments of the frequently encountered elements of the interface - navigation 
panels, various information blocks, etc.

```php
namespace controllers;

use nadir\core\AbstractWebCtrl;

class Test extends AbstractWebCtrl
{

    public function actionDefault()
    {
        // ...
        $this->setView('test', 'default');
        $this->setLayout('main');
        $this->getLayout()->isUserOnline = false;
        $this->getView()->foo            = 'foo';
        $this->getView()->bar            = array(42, 'bar');
        // ...
        $this->render();
    }
}
```

In the markup file `/views/views/test/default.php` of this View variables are 
readable by calling `$this->foo` and `$this->bar`.

```
<!-- ... -->
<div>
    <h1><?= $this->foo; ?></h1>
    <?php if (is_array($this->bar) && !empty($this->bar)): ?>
        <ul>
            <?php foreach ($this->bar as $elem): ?>
                <li><?= $elem; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<!-- ... -->
```

Similarly, in the markup file `/views/layouts/main.php` of the Layout, the variable 
is readable by calling `$this->isUserOnline`

```
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Nadir Microframework</title>
    </head>
    <body>
        <?php $this->getView()->render(); ?>
    </body>
</html>
```

Pay attention that the rendering of the View in Layout is determined by the location 
of the method call `$this->getView()->render()`.

### Snippets

Working with snippets, in general, is similar to working with Composites - View 
and Layout. The class of the snippet is also inherits the class `\nadir\core\AbstractView` 
and the process of sending and calling user variables is similar to that of the 
Layout and View. Composites can contain more than one snippet. The snippet can't 
include another snippet. We will take the part of the markup from the previous 
example into the separate snippet `topbar`. The file `/views/snippets/topbar.php` 
will contain the following code:

```
<h1>User <?= $this->isUserOnline ? 'online' : 'offline'; ?></h1>
```

The controller action will look like this:

```php
namespace controllers;

use nadir\core\AbstractWebCtrl;

class Test extends AbstractWebCtrl
{

    public function actionDefault()
    {
        // ...
        $this->setView('test', 'default');
        $this->setLayout('main');
        $this->getView()->addSnippet('topbar');
        $this->getView()
            ->getSnippet('topbar')
            ->isUserOnline               = false;
        $this->getView()->foo            = 'foo';
        $this->getView()->bar            = array(42, 'bar');
        // ...
        $this->render();
    }
}
```

The rendering of the snippet `topbar` in View is determined by the location 
of the method call `$this->getSnippet('topbar')->render()`.

```
<!-- ... -->
<div>
    <?php $this->getSnippet('topbar')->render(); ?>
    <h1><?= $this->foo; ?></h1>
    <?php if (is_array($this->bar) && !empty($this->bar)): ?>
        <ul>
            <?php foreach ($this->bar as $elem): ?>
                <li><?= $elem; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<!-- ... -->
```
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
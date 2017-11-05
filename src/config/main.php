<?php
return array(
    'componentsRootMap'  => array(
        'controllers' => '/Controllers',
        'views'       => '/views/views',
        'layouts'     => '/views/layouts',
        'snippets'    => '/views/snippets',
        'models'      => '/Models',
        'libs'        => '/Libs',
        'vendor'      => '/vendor',
        'images'      => '/assets/imgs',
        'js'          => '/js',
        'css'         => '/css'
    ),
    'autoloadingRootSet' => array(
        '/', // controllers & models autoload
        '/Libs', // core extensions classes autoload
    ),
    'defaultLayout'      => 'main',
//    'db'                 => array(// for example
//        'host'     => '',
//        'username' => '',
//        'password' => '',
//        'dbname'   => ''
//    ),
    'routeMap'           => array(
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
    )
);

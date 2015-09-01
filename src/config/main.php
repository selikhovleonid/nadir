<?php

return array(
    'componentsRootMap'  => array(
        'controllers' => '/controllers',
        'views'       => '/views/views',
        'layouts'     => '/views/layouts',
        'snippets'    => '/views/snippets',
        'models'      => '/models',
        'libs'        => '/libs',
        'vendor'      => '/vendor',
        'images'      => '/assets/imgs',
        'js'          => '/js',
        'css'         => '/css'
    ),
    'autoloadingRootSet' => array(
        '/', // controllers & models autoload
        '/libs', // core extensions classes autoload
//        '/libs/some_dbms_name', // for example
//        '/vendor/some_framework_name/src', // for example
    ),
    'defaultLayout'      => 'main',
//    'db'                 => array(// for example
//        'host'     => '',
//        'username' => '',
//        'password' => '',
//        'dbname'   => ''
//    ),
    'routeMap'           => array(
        'get'    => array(
            '/'  => array(
                'ctrl' => array('Test', 'actionDefault'),
                'auth' => TRUE,
                'role' => 'all',
            ),
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => FALSE,
            ),
        ),
        'post'   => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => FALSE,
            ),
        ),
        'put'    => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => FALSE,
            ),
        ),
        'delete' => array(
            '.*' => array(
                'ctrl' => array('System', 'actionPage404'),
                'auth' => FALSE,
            ),
        ),
    )
);

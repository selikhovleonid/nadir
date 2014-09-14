<?php

return array(
    'componentsRootMap'  => array(
        'controllers' => '/controllers',
        'views'       => '/views',
        'layouts'     => '/layouts',
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
//		'/libs/some_dbms_name', // for example
//		'/vendor/some_framework_name/src', // for example
    ),
    'defaultLayout'      => 'main',
    'page404'            => 'page404',
//	'db'				 => array( // for example
//		'host'		 => '', 
//		'username'	 => '',
//		'password'	 => '',
//		'dbname'	 => ''
//	),
    'routeMap'           => array(
        '/'  => array('Test', 'actionDefault'),
        '.*' => array('System', 'actionPage404')
    )
);

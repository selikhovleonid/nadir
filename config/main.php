<?php

return array(
	'componentsRootMap'	 => array(
		'controllers'	 => '/controllers',
		'views'			 => '/views',
		'layouts'		 => '/layouts',
		'libs'			 => '/libs',
		'vendor'		 => '/vendor',
		'images'		 => '/assets/imgs',
		'js'			 => '/js',
		'css'			 => '/css'
	),
	'autoloadingRootSet' => array(
		'/', // controllers autoload
		'/libs', // model-access classes autoload
//		'/libs/some_dbms_name', // for example
//		'/vendor/some_framework_name/src', // for example
	),
	'defaultLayout'		 => 'main',
	'page404'			 => 'page404',
	'routeMap'			 => array(
		'/'	 => array('Test', 'actionDefault'),
		'.*' => array('System', 'action404')
	)
);

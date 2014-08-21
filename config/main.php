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
		'css'			 => 'css'
	),
	'defaultLayout'		 => 'main',
	'page404'			 => 'page404',
	'routeMap'			 => array(
		'/'	 => array('Test', 'actionDefault'),
		'.*' => array('System', 'action404')
	)
);

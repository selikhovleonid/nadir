<?php

/**
 *
 * @author coon
 */

namespace core;

interface IFrontController {
	
	public static function run();

	public function init();

	public function handleRequest();
}


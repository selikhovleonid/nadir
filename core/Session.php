<?php

/**
 * Description of Session
 *
 * @author coon
 */

namespace core;

class Session {
// Facade
	
	public static function start() {
		@session_start();
		return self::getId();
	}
	
	public static function getId() {
		return session_id();
	}
}


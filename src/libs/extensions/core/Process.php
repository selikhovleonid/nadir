<?php

namespace extensions\core;

use \core\IProcess;

/**
 * The class provides custom configuration loading during the application is starting.
 * It also kills user's processes if it needed. It realized as singleton.
 * @author coon.
 */
class Process implements IProcess {

    /** @var self This's singleton object of current class. */
    private static $_instance = NULL;

    /**
     * @ignore.
     */
    private function __construct() {
        // Nothing here...
    }

    /**
     * It returns the singleton-instance of current class.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * The method contains the realization of the custom configuration init.
     * @return void.
     */
    public function run() {
        // Put your code here;
    }

    /**
     * The method implements the killing of processes launched by the user.
     * @return void.
     */
    public function stop() {
        // Put your code here;
    }

}

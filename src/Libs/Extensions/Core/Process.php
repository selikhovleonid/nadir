<?php

namespace Nadir\Extensions\Core;

use Nadir\Core\ProcessInterface;

/**
 * The class provides custom configuration loading during the application is starting.
 * It also kills user's processes if it needed. It realized as singleton.
 * @author coon.
 */
class Process implements ProcessInterface
{
    /** @var self This's singleton object of current class. */
    private static $instance = null;

    /**
     * @ignore.
     */
    private function __construct()
    {
        // Nothing here...
    }

    /**
     * It returns the singleton-instance of current class.
     * @return self.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The method contains the realization of the custom configuration init.
     * @return void.
     */
    public function run()
    {
        // Put your code here;
    }

    /**
     * The method implements the killing of processes launched by the user.
     * @return void.
     */
    public function stop()
    {
        // Put your code here;
    }
}
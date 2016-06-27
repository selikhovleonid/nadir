<?php

namespace extensions\core;

use \core\IRunnable;

/**
 * This's the abstract auth class.
 * @author coon
 */
abstract class AAuth implements IRunnable {

    /**
     * The method checks if the user auth is valid.
     * @return boolean.
     */
    abstract public function isValid();

    /**
     * The method contains the code those invokes if the auth was failed.
     */
    abstract public function onFail();
}

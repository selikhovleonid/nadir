<?php

namespace extensions\core;

use \core\RunnableInterface;

/**
 * This's the abstract auth class.
 * @author coon
 */
abstract class AbstractAuth implements RunnableInterface
{

    /**
     * The method checks if the user auth is valid.
     * @return boolean.
     */
    abstract public function isValid();

    /**
     * The method contains the code which invokes if the auth was failed.
     */
    abstract public function onFail();
}
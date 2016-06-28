<?php

/**
 * This's an abstract class, which contains functionality of setting, reading and
 * exist checking of the user's variable for a child class. The abstract modifier
 * was set a specially.
 * @author coon
 */

namespace core;

abstract class AUserPropAccessor {

    /** @var array The user's variable pairs map. */
    private $_dataMap = array();

    /**
     * @ignore.
     */
    public function __construct() {
        // Nothing here...
    }

    /**
     * It sets the custom variable (It overrides the magic method).
     * @param string $sKey The variable name.
     * @param mixed $sValue The variable value.
     */
    public function __set($sKey, $sValue) {
        $this->_dataMap[$sKey] = $sValue;
    }

    /**
     * It returns the custom variable if it exists (It overrides the magic method).
     * @param string $sKey The variable name.
     * @return mixed|null
     */
    public function __get($sKey) {
        if (array_key_exists($sKey, $this->_dataMap)) {
            return $this->_dataMap[$sKey];
        } else {
            return NULL;
        }
    }

    /**
     * It overrides the magic method __isset().
     * @param string $sKey This's variable name.
     * @return boolean.
     */
    public function __isset($sKey) {
        return isset($this->_dataMap[$sKey]);
    }

}

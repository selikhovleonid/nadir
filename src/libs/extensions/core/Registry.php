<?php

namespace extensions\core;

/**
 * The singleton instance of current class is the Registry - the global storage 
 * of custom variables, which lifetime is equal to the life cycle time of the
 * scrypt.
 * @author coon
 */
class Registry
{
    /** @var self This's singleton object of current class. */
    private static $instance = null;

    /** @var mixed[] The user's variable storage. */
    protected $store = array();

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
     * It adds the user variable to the storage.
     * @param string $sKey The variable name.
     * @param mixed $mValue The variable value.
     * @return self.
     */
    public function set($sKey, $mValue)
    {
        $this->store[$sKey] = $mValue;
        return self::$instance;
    }

    /**
     * It returns the variable value getted by the name from the storage.
     * @param string $sKey The variable name.
     * @return mixed|null.
     */
    public function get($sKey = '')
    {
        if (empty($sKey)) {
            return $this->store;
        } else {
            return isset($this->store[$sKey]) ? $this->store[$sKey] : null;
        }
    }
}
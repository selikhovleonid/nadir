<?php

namespace core;

/**
 * This's autoloader of the PHP classes, realized as Singleton. It recognized both
 * the naming system adopted in the PEAR packages and the namespace-based system.
 * @author coon.
 */
class Autoloader {

    /** @var string The path to the root of application. */
    private $_appRoot = NULL;

    /** @var self The singleton object of current class. */
    private static $_instance = NULL;

    /** @var array[] The root set. */
    private $_rootSet = array();

    /**
     * @ignore.
     */
    private function __construct() {
        // nothing here
    }

    /**
     * It returns the singleton instance of Autoloader class.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * It checks if the passed root path already enters into root set of 
     * autoloading.
     * @param string $sRoot The directory root relative to the application root.
     * @return boolean.
     */
    private function _isInRootSet($sRoot) {
        foreach ($this->_rootSet as $aSet) {
            if ($aSet['root'] == $sRoot) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * It sets the root of application.
     * @param string $sAppRoot The path to the application root.
     * @return self.
     */
    public function setAppRoot($sAppRoot) {
        $this->_appRoot = $sAppRoot;
        return self::$_instance;
    }

    /**
     * It returns the root of application.
     * @return string.
     */
    public function getAppRoot() {
        return $this->_appRoot;
    }

    /**
     * It returns TRUE if the application root already defined. 
     * @return boolean.
     */
    public function isAppRootSet() {
        return !empty($this->_appRoot);
    }

    /**
     * It adds the directory root for autoloading and checks for duplication.  
     * @param string $sRoot The directory root relative to the application root.
     * @return self.
     * @throws Exception It throws if the application root wasn't define.
     */
    public function add($sRoot) {
        if ($this->isAppRootSet()) {
            $sRoot = trim($sRoot);
            if (substr($sRoot, -1) == DIRECTORY_SEPARATOR) {
                $sRoot = substr($sRoot, 0, -1);
            }
            if (strpos($sRoot, $this->getAppRoot()) === FALSE) {
                $sRoot = $this->getAppRoot() . $sRoot;
            }
            if (!$this->_isInRootSet($sRoot)) {
                $this->_rootSet[] = array(
                    'root'     => $sRoot,
                    'isLoaded' => FALSE
                );
            }
            return self::$_instance;
        } else {
            throw new Exception("Application root isn't define.");
        }
    }

    /**
     * This's the accessor for returning the set of directories root for autoloading.
     * @return string[] Множество зарегистрированных корней.
     */
    public function getRootSet() {
        return $this->_rootSet;
    }

    /**
     * It executes class autoloader functionality. All earlier registered directory 
     * roots become enabled for loading.
     * @return void.
     */
    public function run() {
        foreach ($this->_rootSet as & $aSet) {
            if (!$aSet['isLoaded']) {
                spl_autoload_register(self::_getFuncCall($aSet['root']), TRUE,
                        TRUE);
                $aSet['isLoaded'] = TRUE;
            }
        }
    }

    /**
     * It returns the anonimus closure function for PHP class autoloading (both
     * the naming system adopted in the PEAR packages and the namespace-based 
     * system).
     * @param string $sRoot The directory root relative to the application root.
     * @return callable The closure function.
     */
    private static function _getFuncCall($sRoot) {
        // Currying
        return function ($sClassName) use ($sRoot) {
            if (preg_match('/\\\\/', $sClassName)) {
                $sRelativePath = str_replace('\\', DIRECTORY_SEPARATOR,
                        $sClassName);
            } elseif (preg_match('/_/', $sClassName)) {
                $sRelativePath = str_replace('_', DIRECTORY_SEPARATOR,
                        $sClassName);
            } else {
                $sRelativePath = $sClassName;
            }
            $sPath = $sRoot . DIRECTORY_SEPARATOR . $sRelativePath . '.php';
            if (is_readable($sPath)) {
                require_once $sPath;
            }
        };
    }

}

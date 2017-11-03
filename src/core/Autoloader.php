<?php

namespace core;

/**
 * This's autoloader of the PHP classes, realized as Singleton. It recognized both
 * the naming system adopted in the PEAR packages and the namespace-based system.
 * @author coon.
 */
class Autoloader
{
    /** @var string The path to the root of application. */
    private $appRoot = null;

    /** @var self The singleton object of current class. */
    private static $instance = null;

    /** @var array[] The root set. */
    private $rootSet = array();

    /**
     * @ignore.
     */
    private function __construct()
    {
        // nothing here
    }

    /**
     * It returns the singleton instance of Autoloader class.
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
     * It checks if the passed root path already enters into root set of 
     * autoloading.
     * @param string $sRoot The directory root relative to the application root.
     * @return boolean.
     */
    private function inRootSet($sRoot)
    {
        foreach ($this->rootSet as $aSet) {
            if ($aSet['root'] == $sRoot) {
                return true;
            }
        }
        return false;
    }

    /**
     * It sets the root of application.
     * @param string $sAppRoot The path to the application root.
     * @return self.
     */
    public function setAppRoot($sAppRoot)
    {
        $this->appRoot = $sAppRoot;
        return self::$instance;
    }

    /**
     * It returns the root of application.
     * @return string.
     */
    public function getAppRoot()
    {
        return $this->appRoot;
    }

    /**
     * It returns TRUE if the application root already defined. 
     * @return boolean.
     */
    public function isAppRootSet()
    {
        return !empty($this->appRoot);
    }

    /**
     * It adds the directory root for autoloading and checks for duplication.  
     * @param string $sRoot The directory root relative to the application root.
     * @return self.
     * @throws Exception It's throwed if the application root wasn't defined.
     */
    public function add($sRoot)
    {
        if (!$this->isAppRootSet()) {
            throw new Exception("Application root isn't defined.");
        }
        if ($this->isAppRootSet()) {
            $sRoot = trim($sRoot);
            if (substr($sRoot, -1) == DIRECTORY_SEPARATOR) {
                $sRoot = substr($sRoot, 0, -1);
            }
            if (strpos($sRoot, $this->getAppRoot()) === false) {
                $sRoot = $this->getAppRoot().$sRoot;
            }
            if (!$this->inRootSet($sRoot)) {
                $this->rootSet[] = array(
                    'root'     => $sRoot,
                    'isLoaded' => false
                );
            }
            return self::$instance;
        }
    }

    /**
     * This's the accessor for returning the set of directories root for autoloading.
     * @return string[] The set of registered roots.
     */
    public function getRootSet()
    {
        return $this->rootSet;
    }

    /**
     * It executes class autoloader functionality. All earlier registered directory 
     * roots become enabled for loading.
     * @return void.
     */
    public function run()
    {
        foreach ($this->rootSet as & $aSet) {
            if (!$aSet['isLoaded']) {
                spl_autoload_register(self::getFuncCall($aSet['root']), true,
                    true);
                $aSet['isLoaded'] = true;
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
    private static function getFuncCall($sRoot)
    {
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
            $sPath = $sRoot.DIRECTORY_SEPARATOR.$sRelativePath.'.php';
            if (is_readable($sPath)) {
                require_once $sPath;
            }
        };
    }
}
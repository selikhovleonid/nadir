<?php

/**
 * The application helper class, which provides config data loading and general 
 * access to them. It realized as Singleton.
 * @author coon.
 */

namespace core;

class AppHelper extends AAutoAccessors implements IRunnable {

    /** @var string The path to the application root. */
    public $appRoot = NULL;

    /** @var string The path to the configuration file. */
    public $configFile = NULL;

    /** @var string The route configuration. */
    public $routeConfig = NULL;

    /** @var self The singleton-object of current class. */
    private static $_instance = NULL;

    /** @var mixed[] The configuration data set. */
    private $_configSet = array();

    /** @var string The site basic URL. */
    private $_siteBaseUrl = NULL;

    /**
     * The closed class constructor. It determines  the site basic URL.
     * @return self.
     */
    private function __construct() {
        $this->_siteBaseUrl = self::_getBaseUrl();
    }

    /**
     * It retrurns the current class instance.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * This's mutator-method. It sets the path to the root of application.
     * @param string $sRoot The path to the application root.
     * @return self.
     */
    public function setAppRoot($sRoot) {
        $this->appRoot = $sRoot;
        return self::$_instance;
    }

    /**
     * It sets the path to the main configuration of application file relative
     * to its root.
     * @param string $sFilePath.
     * @return self.
     */
    public function setConfigFile($sFilePath) {
        $this->configFile = $sFilePath;
        return self::$_instance;
    }

    /**
     * It loads main file of configuration and checks for validity. 
     * @return self.
     * @throws Exception.
     */
    public function run() {
        if (!$this->isAppRootSet()) {
            throw new Exception("The application root isn't define.", 1);
        }
        if (!$this->isConfigFileSet()) {
            throw new Exception("Main config file isn't define.", 2);
        }
        $sConfigPath = $this->getAppRoot() . $this->getConfigFile();
        if (!is_readable($sConfigPath)) {
            throw new Exception("It's unable to load " . $sConfigPath
            . 'as main config file.', 4);
        }
        $mConfig = include $sConfigPath;
        if (!is_array($mConfig)) {
            throw new Exception('Main config must be array.', 5);
        }
        $this->_configSet = $mConfig;
    }

    /**
     * It returns the config value by passed name or all config set if it wasn't
     * specified.
     * @param string $sName The config name.
     * @return array|null.
     */
    public function getConfig($sName = '') {
        if (empty($sName)) {
            return $this->_configSet;
        } elseif (isset($this->_configSet[$sName])) {
            return $this->_configSet[$sName];
        } else {
            return NULL;
        }
    }

    /**
     * It determines the basic site URL.
     * @return string.
     */
    private static function _getBaseUrl() {
        if (isset($_SERVER['SERVER_NAME'])) {
            $sProtocol = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on'
                    ? 'https'
                    : 'http';
            return $sProtocol . '://' . $_SERVER['SERVER_NAME'];
        }
        return NULL;
    }

    /**
     * This's method-accessor to basic site URL.
     * @return string.
     */
    public function getSiteBaseUrl() {
        return $this->_siteBaseUrl;
    }

    /**
     * The method returns absolute or relative path (URL) to the component by 
     * passed name. The absolute URL used to determs path to assets (media-data) 
     * as usual.
     * @param string $sName The component name.
     * @param boolean $fAsAbsolute The optional flag is equal TRUE by default. 
     * @return string.
     */
    public function getComponentUrl($sName, $fAsAbsolute = TRUE) {
        $aRootMap = $this->getConfig('componentsRootMap');
        $sSiteUrl = $fAsAbsolute
                ? $this->_siteBaseUrl
                : '';
        return isset($aRootMap[$sName])
                ? $sSiteUrl . $aRootMap[$sName]
                : NULL;
    }

    /**
     * The method returns full path to the parent directory of component by its
     * name.
     * @param string $sName The component name.
     * @return string|null.
     */
    public function getComponentRoot($sName) {
        $aRootMap = $this->getConfig('componentsRootMap');
        return isset($aRootMap[$sName])
                ? $this->getAppRoot() . $aRootMap[$sName]
                : NULL;
    }

}

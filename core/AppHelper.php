<?php

/**
 * Класс-помощник-приложения, отвечающий за загрузку данных конфигурации и 
 * обеспечивающий общий доступ приложения к ним. Реализован как Singleton.
 * @author coon
 */

namespace core;

class AppHelper implements IRunnable {

    /** @var string Путь к кореню веб-приложения. */
    private $_appRoot = NULL;

    /** @var self Объект-singleton текущего класса. */
    private static $_instance = NULL;

    /** @var mixed[] Множество конфигураций. */
    private $_configSet = array();

    /** @var mixed[] Шаблон для валидации основной конфигурации. */
    private $_mainConfigPattern = array();

    /** @var string Базовый URL сайта. */
    private $_siteBaseUrl = '';

    private function __construct() {
        
    }

    /**
     * Возвращает singleton-экземпляр текущего класса.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Устанавливает корень веб-приложения.
     * @param string $sAppRoot Путь к корню приложения.
     * @return self.
     */
    public function setAppRoot($sAppRoot) {
        $this->_appRoot = $sAppRoot;
        return self::$_instance;
    }

    /**
     * Возвращает корень веб-приложения.
     * @return string.
     */
    public function getAppRoot() {
        return $this->_appRoot;
    }

    /**
     * Возвращает TRUE, если корень веб-приложения определен.
     * @return boolean.
     */
    public function isAppRootSet() {
        return !empty($this->_appRoot);
    }

    /**
     * Загружает основной конфигурационный файл и выполняет проверку валидности.
     * @todo В настоящее время валидация формально прописана, класс Validator 
     * не содержит функциональности. Возможно, следует использовать
     * сторонний валидатор.
     * @return self.
     * @throws Exception.
     */
    public function run() {
        if (!$this->isAppRootSet()) {
            throw new Exception("Application root isn't define.");
        }
        $this->_siteBaseUrl = self::_getBaseUrl();
        $sConfigDir         = $this->_appRoot . DIRECTORY_SEPARATOR . 'config';
        $sConfigPath        = $sConfigDir . DIRECTORY_SEPARATOR . 'main.php';
        $sPatternPath       = $sConfigDir . DIRECTORY_SEPARATOR . 'pattern.php';
        if (!is_readable($sConfigPath) || !is_readable($sPatternPath)) {
            throw new Exception('Unable load ' . $sConfigPath
            . 'as main config file or' . $sPatternPath . 'as its pattern.');
        }
        $mConfig  = include $sConfigPath;
        $mPattern = include $sPatternPath;
        if (!is_array($mConfig) || !is_array($mPattern)) {
            throw new Exception('Main config and its pattern shall be arrays.');
        }
        $oValidator = new Validator($mPattern);
        if ($oValidator->isValid($mConfig)) {
            $this->_configSet         = $mConfig;
            $this->_mainConfigPattern = $mPattern;
            return self::$_instance;
        } else {
            throw new Exception("Main config isn't valid.");
        }
    }

    /**
     * Возвращает конкретную конфигурацию по имени либо всю конфигурацию как массив.
     * @param string $sName Имя конфигурации.
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
     * Возвращает шаблон для валидации основной конфигурации приложения.
     * @return array
     */
    public function getConfigPattern() {
        return $this->_mainConfigPattern;
    }

    /**
     * Определят базовый URL сайта.
     * @return string.
     */
    private static function _getBaseUrl() {
        $sProtocol = !empty($_SERVER['HTTPS']) 
            && strtolower($_SERVER['HTTPS']) == 'on' 
            ? 'https' 
            : 'http';
        return $sProtocol . '://' . $_SERVER['SERVER_NAME'];
    }

    /**
     * Метод-аксессор, возвращающий базовый URL сайта.
     * @return string.
     */
    public function getSiteBaseUrl() {
        return $this->_siteBaseUrl;
    }

    /**
     * Метод возвращает абсолютный или относительный путь (URL) к компоненту по
     * его имени. Полный URL обычно требуется для определения пути к медиа-файлам 
     * (директория assets).
     * @param string $sName.
     * @param boolean $fAsAbsolute Флаг по умолчанию принимает значение TRUE. 
     * @return string.
     */
    public function getComponentUrl($sName, $fAsAbsolute = TRUE) {
        $aRootMap = $this->getConfig('componentsRootMap');
        $sSiteUrl = $fAsAbsolute ? $this->_siteBaseUrl : '';
        return isset($aRootMap[$sName]) 
            ? $sSiteUrl . $aRootMap[$sName] 
            : NULL;
    }

    /**
     * Метод возвращает полный путь к родительской директории компонента по его 
     * имени.
     * @param string $sName Имя компонента.
     * @return string|null.
     */
    public function getComponentRoot($sName) {
        $aRootMap = $this->getConfig('componentsRootMap');
        return isset($aRootMap[$sName]) 
            ? $this->_appRoot . $aRootMap[$sName] 
            : NULL;
    }

}
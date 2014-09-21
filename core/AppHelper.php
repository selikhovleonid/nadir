<?php

/**
 * Класс-помощник-приложения, отвечающий за загрузку данных конфигурации и 
 * обеспечивающий общий доступ приложения к ним. Реализован как Singleton.
 * @author coon.
 */

namespace core;

class AppHelper extends AAutoAccessors implements IRunnable {

    /** @var string Путь к кореню веб-приложения. */
    public $appRoot = NULL;

    /** @var string Путь к корню файла конфигурации. */
    public $configFile = NULL;

    /** @var string Путь к корню файла с шаблоном валидации. */
    public $patternFile = NULL;

    /** @var self Объект-singleton текущего класса. */
    private static $_instance = NULL;

    /** @var mixed[] Множество конфигураций. */
    private $_configSet = array();

    /** @var mixed[] Шаблон для валидации основной конфигурации. */
    private $_mainConfigPattern = array();

    /** @var string Базовый URL сайта. */
    private $_siteBaseUrl = NULL;

    /**
     * Закрытый конструктор, определяет базовый URL сайта при создании 
     * объекта-одиночки.
     * @return self.
     */
    private function __construct() {
        $this->_siteBaseUrl = self::_getBaseUrl();
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
     * Метод-мутатор. Устанавливает путь к корню веб-приложения.
     * @param string $sRoot.
     * @return self.
     */
    public function setAppRoot($sRoot) {
        $this->appRoot = $sRoot;
        return self::$_instance;
    }

    /**
     * Устанавливает путь к файлу с основной конфигурацией приложения относительно
     * его корня.
     * @param string $sFilePath.
     * @return self.
     */
    public function setConfigFile($sFilePath) {
        $this->configFile = $sFilePath;
        return self::$_instance;
    }

    /**
     * Устанавливает путь к файлу шаблона валидации конфигурации приложения
     * относительно корня приложения.
     * @param string $sFilePath.
     * @return self.
     */
    public function setPatternFile($sFilePath) {
        $this->patternFile = $sFilePath;
        return self::$_instance;
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
            throw new Exception("Application root isn't define.", 1);
        }
        if (!$this->isConfigFileSet()) {
            throw new Exception("Main config file isn't define.", 2);
        }
        if (!$this->isPatternFileSet()) {
            throw new Exception("Pattern file for main config isn't define.", 3);
        }
        $sConfigPath  = $this->getAppRoot() . $this->getConfigFile();
        $sPatternPath = $this->getAppRoot() . $this->getPatternFile();
        if (!is_readable($sConfigPath) || !is_readable($sPatternPath)) {
            throw new Exception('Unable load ' . $sConfigPath
            . 'as main config file or' . $sPatternPath . 'as its pattern.', 4);
        }
        $mConfig  = include $sConfigPath;
        $mPattern = include $sPatternPath;
        if (!is_array($mConfig) || !is_array($mPattern)) {
            throw new Exception('Main config and its pattern shall be arrays.', 5);
        }
        $oValidator = new Validator($mPattern);
        if ($oValidator->isValid($mConfig)) {
            $this->_configSet         = $mConfig;
            $this->_mainConfigPattern = $mPattern;
            return self::$_instance;
        } else {
            throw new Exception("Main config isn't valid.", 6);
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
            && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
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
     * @param boolean $fAsAbsolute Optional Флаг по умолчанию равен TRUE. 
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
            ? $this->getAppRoot() . $aRootMap[$sName] 
            : NULL;
    }

}
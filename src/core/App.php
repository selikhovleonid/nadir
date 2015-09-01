<?php

namespace core;

/**
 * Класс определяет центральную точку входа для всех запросов, создает 
 * конфигурированное веб приложение. 
 * Реализует шаблон Front Controller, является Singleton-ом.
 * @author coon
 */
class App extends AAutoAccessors implements IFrontController, IRunnable {

    /** @var string Путь к корню файла конфигурации. */
    public $configFile = NULL;

    /** @var self Объект-singleton текущего класса. */
    private static $_instance = NULL;

    /**
     * @ignore.
     */
    private function __construct() {
        // nothing here...
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
     * {@inheritdoc}
     */
    public function run() {
        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public function init() {
        $this->_initHelper();
        $this->_initAutoload();
        $this->_initUserProcess();
        $this->handleRequest();
        $this->_stopUserProcess();
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest() {
        $oRequest      = new Request();
        $oCtrlResolver = new CtrlResolver($oRequest);
        $oCtrlResolver->run();
    }

    /**
     * Инициализирует Помощник приложения.
     * @return void.
     */
    private function _initHelper() {
        if (!$this->isConfigFileSet()) {
            throw new Exception("Main config file isn't define.", 2);
        }
        AppHelper::getInstance()
                ->setAppRoot(Autoloader::getInstance()->getAppRoot())
                ->setConfigFile($this->getConfigFile())
                ->run();
    }

    /**
     * Инициализация автоподгрузки всех классов приложения. Объект 
     * Автоподгрузчика получает все корни директорий из объекта 
     * Помощника приложения, после чего связывает их с автоподгрузкой. 
     * @return void.
     */
    private function _initAutoload() {
        $mRoot = AppHelper::getInstance()->getConfig('autoloadingRootSet');
        foreach ($mRoot ? : array() as $sRoot) {
            Autoloader::getInstance()->add($sRoot);
        }
        Autoloader::getInstance()->run();
    }

    /**
     * Метод выполняет запуск пользовательских процессов.
     * @return void.
     */
    private function _initUserProcess() {
        \extensions\core\Process::getInstance()->run();
    }

    /**
     * Метод останавливает пользовательские процессы.
     * @return void.
     */
    private function _stopUserProcess() {
        \extensions\core\Process::getInstance()->stop();
    }

}

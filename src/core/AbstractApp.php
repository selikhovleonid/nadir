<?php

namespace nadir\core;

use extensions\core\Process;

/**
 * This's an abstract application class. It determines the central entry point for
 * the all requests, creates the configured application. It implements Front 
 * Controller pattern, it's Singleton-instance.
 * @author coon
 */
abstract class AbstractApp extends AbstractAutoAccessors implements FrontControllerInterface,
    RunnableInterface
{
    /** @var string This's path to the config file root. */
    public $configFile = null;

    /** @var string The path to the root of application. */
    public $appRoot = null;

    /** @var self This's singleton object of the current class. */
    protected static $instance = null;

    /**
     * @ignore.
     */
    protected function __construct()
    {
        // Nothing here...
    }

    /**
     * It returns the context called singleton-instance. It implements late static
     * binding.
     * @return self.
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * It sets the path to the main application config file referenced to its root.
     * @param string $sFilePath.
     * @return self.
     */
    public function setConfigFile($sFilePath)
    {
        $this->configFile = $sFilePath;
        return static::$instance;
    }

    /**
     * It sets the root of application.
     * @param string $sAppRoot The path to the application root.
     * @return self.
     */
    public function setAppRoot($sAppRoot)
    {
        $this->appRoot = $sAppRoot;
        return static::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->initHelper();
        $this->initUserProcess();
        $this->handleRequest();
        $this->stopUserProcess();
    }

    /**
     * {@inheritdoc}
     */
    abstract public function handleRequest();

    /**
     * It inits the application helper.
     * @return void.
     */
    private function initHelper()
    {
        if (!$this->isConfigFileSet()) {
            throw new Exception("The main config file wasn't defined.");
        }
        if (!$this->isAppRootSet()) {
            throw new Exception("The application root wasn't defined.");
        }
        AppHelper::getInstance()
            ->setAppRoot($this->getAppRoot())
            ->setConfigFile($this->getConfigFile())
            ->run();
    }

    /**
     * The method runs custom processes.
     * @return void.
     */
    private function initUserProcess()
    {
        Process::getInstance()->run();
    }

    /**
     * The method kills user's processes.
     * @return void.
     */
    private function stopUserProcess()
    {
        Process::getInstance()->stop();
    }
}
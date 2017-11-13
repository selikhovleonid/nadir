<?php

namespace nadir\core;

/**
 * This is an abstract application class. It determines the central entry point for
 * the all requests, creates the configured application. It implements Front 
 * Controller pattern, it's Singleton-instance.
 * @author Leonid Selikhov
 */
abstract class AbstractApp extends AbstractAutoAccessors implements FrontControllerInterface,
    RunnableInterface
{
    /** @var string This is path to the config file root. */
    public $configFile = '';

    /** @var string The path to the root of application. */
    public $appRoot = '';

    /** @var \nadir\core\ProcessInterface The user defined Process object. */
    public $customProcess = null;

    /** @var self This is singleton object of the current class. */
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
     * It sets the custom Process.
     * @param \nadir\core\ProcessInterface $oProcess The user defined Process object.
     * @return self.
     */
    public function setCustomProcess(ProcessInterface $oProcess)
    {
        $this->customProcess = $oProcess;
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
        $this->runCustomProcess();
        $this->handleRequest();
        $this->stopCustomProcess();
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
     * This method runs custom process. The highest priority has a Process that
     * has been set by the setCustomProcess() method. If it wasn't set, it inits
     * the default Process from the project skeleton extension.
     * @return void.
     */
    private function runCustomProcess()
    {
        if ($this->isCustomProcessSet()) {
            $this->getCustomProcess()->run();
        } elseif (class_exists('\extensions\core\Process')) {
            \extensions\core\Process::getInstance()->run();
        }
    }

    /**
     * The method kills custom process.
     * @return void.
     */
    private function stopCustomProcess()
    {
        if ($this->isCustomProcessSet()) {
            $this->getCustomProcess()->stop();
        } elseif (class_exists('\extensions\core\Process')) {
            \extensions\core\Process::getInstance()->stop();
        }
    }
}
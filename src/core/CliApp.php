<?php

namespace nadir\core;

/**
 * This's a class of cli-application (command line interface application).
 * @author coon
 */
class CliApp extends AbstractApp
{
    /** @var self The singleton object of current class. */
    protected static $instance = NULL;

    /**
     * @ignore.
     */
    protected function __construct()
    {
        // Nothing here...
    }

    /**
     * It processes the call parameters of cli-script and passed them to the 
     * CliCtrlResolver object.
     * @global string[] $argv The array of passed to cli-scrypt args.
     * @throws \core\Exception It throws if it was attempting to call cli-scprit 
     * out the command line interface. 
     */
    public function handleRequest()
    {
        global $argv;
        if (!is_array($argv) || empty($argv)) {
            throw new Exception("Invalid value of the cli args array was given.");
        }
        $oCtrlResolver = new CliCtrlResolver($argv);
        $oCtrlResolver->run();
    }
}
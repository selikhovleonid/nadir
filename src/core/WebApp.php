<?php

namespace nadir\core;

/**
 * It's the web-application class. It specifies an abstract application.
 * @author Leonid Selikhov
 */
class WebApp extends AbstractApp
{
    /** @var self The singleton object of the current class. */
    protected static $instance = null;

    /**
     * @ignore.
     */
    protected function __construct()
    {
        // nothing here...
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest()
    {
        $oRequest      = new Request();
        $oCtrlResolver = new WebCtrlResolver($oRequest);
        $oCtrlResolver->run();
    }
}

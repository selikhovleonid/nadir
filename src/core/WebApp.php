<?php

namespace core;

/**
 * It's the web-application class. It specifies an abstract application.
 * @author coon
 */
class WebApp extends AApplication {

    /** @var self The singleton object of the current class. */
    protected static $_instance = NULL;

    /**
     * @ignore.
     */
    protected function __construct() {
        // nothing here...
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest() {
        $oRequest      = new Request();
        $oCtrlResolver = new WebCtrlResolver($oRequest);
        $oCtrlResolver->run();
    }

}

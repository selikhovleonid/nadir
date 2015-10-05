<?php

/**
 * Демонстрационный класс авторизации
 *
 * @author coon
 */

namespace extensions\core;

use core\Request;
use core\AppHelper;

class Auth extends AAuth {

    protected $request     = NULL;
    protected $routeConfig = NULL;
    protected $error       = NULL;

    public function __construct(Request $oRequest) {
        $this->request     = $oRequest;
        $this->routeConfig = AppHelper::getInstance()->getRouteConfig();
    }

    protected function checkCookies(array $aCookies) {
        // put your code here...
    }

    public function run() {
        if (!isset($this->routeConfig['auth'])) {
            throw new \Exception("Undefined option 'auth' for current route.");
        }
        $mCookies = $this->request->getCookies();
        $this->checkCookies(!is_null($mCookies) ? $mCookies : array());
    }

    public function isValid() {
        return is_null($this->error);
    }

    public function onFail() {
        // put your code here...
    }

}

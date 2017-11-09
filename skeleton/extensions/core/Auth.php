<?php
/**
 * This's the auth general class for custom extension.
 *
 * @author coon
 */

namespace extensions\core;

use nadir\core\Request;
use nadir\core\AppHelper;

class Auth extends AbstractAuth
{
    protected $request     = null;
    protected $routeConfig = null;
    protected $error       = null;

    public function __construct(Request $oRequest)
    {
        $this->request     = $oRequest;
        $this->routeConfig = AppHelper::getInstance()->getRouteConfig();
    }

    protected function checkCookies(array $aCookies)
    {
        // put your code here...
    }

    public function run()
    {
        if (!isset($this->routeConfig['auth'])) {
            throw new \Exception("Undefined option 'auth' for the current route.");
        }
        $mCookies = $this->request->getCookies();
        $this->checkCookies(!is_null($mCookies) ? $mCookies : array());
    }

    public function isValid()
    {
        return is_null($this->error);
    }

    public function onFail()
    {
        // put your code here...
    }
}
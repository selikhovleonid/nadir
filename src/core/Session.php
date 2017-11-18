<?php

namespace nadir\core;

/**
 * This is facade class for working with session.
 * @author Leonid Selikhov
 */
class Session implements ArrayCollectionInterface
{

    /**
     * @ignore.
     */
    public function __construct()
    {
        // Nothing here...
    }

    /**
     * It returns the current ident of session.
     * @return string Id сессии.
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * It checks if the session was started.
     * @return boolean.
     */
    public function isStarted()
    {
        return $this->getId() !== '';
    }

    /**
     * It sets the ident of current session.
     * @param string $iSess The swssion id.
     * @return void.
     */
    public function setId($iSess)
    {
        @session_id($iSess);
    }

    /**
     * It returns the name of current session.
     * @return string.
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * The method sets the name of current session.
     * @param string $sName By default it's PHPSESSID.
     * @throws Exception It throws if passed name consists digits only or is empty.
     */
    public function setName($sName)
    {
        if (!empty($sName)) {
            if (!is_numeric($sName)) {
                @session_name($sName);
            } else {
                throw new Exception('The session name can\'t consist only of digits, '
                .'at least one letter must be presented.');
            }
        } else {
            throw new Exception('Empty session name value was passed.');
        }
    }

    /**
     * It inits the data of new session or continues the current session.
     * @param string $sSessName The optional name of session, it has higher priority
     * than the $iSess parameter.
     * @param string $iSess The optional session ident. It ignored if the $sSessName
     * parameter was passed.
     * @return string The id of current session.
     */
    public function start($sSessName = null, $iSess = null)
    {
        if (!$this->isStarted()) {
            if (!is_null($sSessName)) {
                $this->setName($sSessName);
            }
            @session_start($iSess);
        };
        return $this->getId();
    }

    /**
     * It commits the data of session and closes it.
     * @return void|null.
     */
    public function commit()
    {
        if ($this->isStarted()) {
            session_commit();
        }
    }

    /**
     * It destroys the session data.
     * @return boolean|null The result of destruction.
     */
    public function destroy()
    {
        $mRes = null;
        if ($this->isStarted()) {
            @session_unset();
            $mRes = session_destroy();
        }
        return $mRes;
    }

    /**
     * It complitly destroys session with cookie.
     * @return boolean|null The result.
     */
    public function destroyWithCookie()
    {
        $mRes = null;
        if ($this->isStarted()) {
            $this->destroy();
            $mRes = setcookie($this->getName(), '', time() - 1, '/');
        }
        return $mRes;
    }

    /**
     * It adds the variable to the session.
     * @param string $sKey The name of variable.
     * @param mixed $mValue The value of it.
     * @return void.
     */
    public function add($sKey, $mValue)
    {
        $_SESSION[$sKey] = $mValue;
    }

    /**
     * It adds the array of variables (the key-value pairs) to the session.
     * @param array $aData
     * @return void.
     */
    public function addAll(array $aPairs)
    {
        foreach ($aPairs as $sKey => $mValue) {
            $this->add($sKey, $mValue);
        }
    }

    /**
     * It returns TRUE if the variable with passed key contains into the session.
     * @param string $sKey.
     * @return boolean.
     */
    public function contains($sKey)
    {
        return isset($_SESSION[$sKey]);
    }

    /**
     * It returns TRUE if the session is empty.
     * @return boolean.
     */
    public function isEmpty()
    {
        return empty($_SESSION);
    }

    /**
     * It returns the variable of session value by passed key.
     * @param string $sKey.
     * @return mixed|null.
     */
    public function get($sKey)
    {
        return $this->contains($sKey) ? $_SESSION[$sKey] : null;
    }

    /**
     * It returns the list of session variables.
     * @return string [].
     */
    public function getKeys()
    {
        return array_keys($_SESSION);
    }

    /**
     * It returns all session variables as associative array.
     * @return mixed[].
     */
    public function getAll()
    {
        $aRes = array();
        foreach ($this->getKeys() as $sKey) {
            $aRes[$sKey] = $this->get($sKey);
        }
        return $aRes;
    }

    /**
     * It removes the variable of session by passed key.
     * @param string $sKey.
     * @return mixed|null The removed variable value.
     */
    public function remove($sKey)
    {
        if ($this->contains($sKey)) {
            $mRes = $_SESSION[$sKey];
            unset($_SESSION[$sKey]);
            return $mRes;
        } else {
            return null;
        }
    }

    /**
     * It clears the session by removing all stored variables.
     * @return mixed[] The array of removed vars.
     */
    public function removeAll()
    {
        $aRes = array();
        foreach ($this->getKeys() as $sKey) {
            $aRes[$sKey] = $this->remove($sKey);
        }
        return $aRes;
    }

    /**
     * It returns the count of variables containing into the session.
     * @return integer.
     */
    public function size()
    {
        return count($this->getKeys());
    }
}

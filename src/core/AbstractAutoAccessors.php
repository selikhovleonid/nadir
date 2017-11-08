<?php

namespace nadir\core;

/**
 * The class provides auto method-accessors (get-, set- and isSet- methods) 
 * generation to the public properties of the children classes.
 * @author coon
 */
class AbstractAutoAccessors
{

    /**
     * It's a reflection method, which checks a availability and accessibility
     * of the properties of the child-class.
     * @param type $sPropName The method name.
     * @return boolean
     */
    private function isPropChecked($sPropName)
    {
        $fRes        = false;
        $sClassName  = get_class($this);
        $oReflection = new \ReflectionClass($sClassName);
        if ($oReflection->hasProperty($sPropName) && $oReflection->getProperty($sPropName)->isPublic()
        ) {
            $fRes = true;
        }
        unset($oReflection);
        return $fRes;
    }

    /**
     * This's interceptor method, which catches the calls of undeclared methods of
     * the class. If the name of the invoked method matches the setProperty, getProperty
     * or isPropertySet pattern and the target class has corresponding public 
     * property, then it calls needed accessor as if it was declared directly in 
     * the child-class. In another case it throws exception.
     * @param string $sName The name of the method.
     * @param mixed[] $aArgs The array of passed args.
     * @return mixed|boolean The result is mixed for the getters and setters, is 
     * boolean for isSets.
     * @throws Exception.
     */
    public function __call($sName, $aArgs)
    {
        // Lambda-function
        $funcGenException = function($sClassName, $sPropName) {
            throw new Exception('Undefined or non public property '
            ."{$sClassName}::\${$sPropName}");
        };

        if (preg_match('#^get(\w+)$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->isPropChecked($sPropName)) {
                return $this->$sPropName;
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } elseif (preg_match('#^set(\w+)$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->isPropChecked($sPropName)) {
                $this->$sPropName = $aArgs[0];
                return $aArgs[0];
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } elseif (preg_match('#^is(\w+)Set$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->isPropChecked($sPropName)) {
                return !is_null($this->$sPropName);
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } else {
            $sClassName = get_class($this);
            throw new Exception("Call to undefined method {$sClassName}::{$sName}");
        }
    }
}
<?php

namespace core;

/**
 * This's the class of developer's tools.
 * @author coon.
 */
class Tools
{
    /** @var integer The constant determines the count of spaces to indent. */
    const SPACES_PER_TAB = 4;

    /**
     * @ignore.
     */
    public function __construct()
    {
        // Nothing here...
    }

    /**
     * It returns of string representation of array at the current level of the 
     * tree. It's recursive method.
     * @param type $mVar The variable.
     * @param type $nDepth Optional The max depth of tree print.
     * @param type $nLevel Optional The current level of the tree.
     * @param mixed[] $aObjects Optional The array of variable ojects.
     * @return string.
     */
    private static function getDumpArrayIteration(&$mVar, $nDepth = 10,
                                                  $nLevel = 0,
                                                  array& $aObjects = array()
    )
    {
        $sOut       = '';
        $sSpacesOut = str_repeat(' ', self::SPACES_PER_TAB * $nLevel);
        if ($nDepth <= $nLevel) {
            $sOut .= "array\n{$sSpacesOut}(...)";
        } elseif (empty($mVar)) {
            $sOut .= 'array()';
        } else {
            $sSpacesIn = $sSpacesOut.str_repeat(' ', self::SPACES_PER_TAB);
            $sOut      .= "array\n{$sSpacesOut}(";
            foreach ($mVar as $sKey => $mValue) {
                $sOut .= "\n{$sSpacesIn}"
                    .self::getDumpIteration($sKey, $nDepth, 0, $aObjects)
                    .' => '
                    .self::getDumpIteration($mValue, $nDepth, $nLevel + 1,
                        $aObjects);
            }
            $sOut .= "\n{$sSpacesOut})";
        }
        return $sOut;
    }

    /**
     * It returns the string representation of object at the current level of the 
     * tree. It's recursive method.
     * @param type $mVar The variable.
     * @param type $nDepth Optional The max depth of tree print.
     * @param type $nLevel Optional The current level of the tree.
     * @param mixed[] $aObjects Optional The array of variable ojects.
     * @return string.
     */
    private static function getDumpObjIteration(&$mVar, $nDepth = 10,
                                                $nLevel = 0,
                                                array& $aObjects = array()
    )
    {
        $sOut       = '';
        $sClassName = get_class($mVar);
        $sSpacesOut = str_repeat(' ', self::SPACES_PER_TAB * $nLevel);
        if (($iObj       = array_search($mVar, $aObjects, TRUE)) !== FALSE) {
            $sOut .= "{$sClassName} object #"
                .($iObj + 1)
                ."\n{$sSpacesOut}(...)";
        } elseif ($nDepth <= $nLevel) {
            $sOut .= "{$sClassName} object"
                ."\n{$sSpacesOut}(...)";
        } else {
            // Возвращает модификаторы свойств объекта.
            $funcGetPropMod = function(\ReflectionProperty $oProp) {
                if ($oProp->isPublic()) {
                    $sOut = 'public';
                } elseif ($oProp->isProtected()) {
                    $sOut = 'protected';
                } else {
                    $sOut = 'private';
                }
                if ($oProp->isStatic()) {
                    $sOut .= ' static';
                }
                return $sOut;
            };

            $iObj        = array_push($aObjects, $mVar);
            $sSpacesIn   = $sSpacesOut.str_repeat(' ', self::SPACES_PER_TAB);
            $oReflection = new \ReflectionClass($sClassName);
            $aProps      = $oReflection->getProperties();
            $sOut        .= "{$sClassName} object #{$iObj}\n{$sSpacesOut}(";
            foreach ($aProps as $oProp) {
                $oProp->setAccessible(TRUE);
                $sOut .= "\n{$sSpacesIn}"
                    .'['
                    .self::getDumpIteration($oProp->getName(), $nDepth, 0,
                        $aObjects)
                    .':'.$funcGetPropMod($oProp)
                    .'] => '
                    .self::getDumpIteration($oProp->getValue($mVar), $nDepth,
                        $nLevel + 1, $aObjects);
                if (!$oProp->isPublic()) {
                    $oProp->setAccessible(FALSE);
                }
            }
            $sOut .= "\n{$sSpacesOut})";
            unset($funcGetPropMod);
            unset($oReflection);
        }
        return $sOut;
    }

    /**
     * The method returns the string representation of current level of the tree.
     * It's recursive.
     * @param type $mVar Переменная.
     * @param type $nDepth Optional The max depth of tree print.
     * @param type $nLevel Optional The current level of the tree.
     * @param mixed[] $aObjects Optional The array of variable ojects.
     * @return string.
     */
    private static function getDumpIteration(&$mVar, $nDepth = 10, $nLevel = 0,
                                             array& $aObjects = array()
    )
    {
        $sOut = '';
        switch (gettype($mVar)) {
            case 'NULL':
                $sOut .= 'NULL';
                break;
            case 'boolean':
                $sOut .= $mVar ? 'TRUE' : 'FALSE';
                break;
            case 'integer':
            case 'double':
                $sOut .= (string) $mVar;
                break;
            case 'string':
                $sOut .= "'".addslashes($mVar)."'";
                break;
            case 'unknown type':
                $sOut .= '{unknown}';
                break;
            case 'resource':
                $sOut .= '{resource}';
                break;
            case 'array':
                $sOut .= self::getDumpArrayIteration($mVar, $nDepth, $nLevel,
                        $aObjects);
                break;
            case 'object':
                $sOut .= self::getDumpObjIteration($mVar, $nDepth, $nLevel,
                        $aObjects);
                break;
            default:
                break;
        }
        return $sOut;
    }

    /**
     * It returns the human-readable data of the variable (the variable dump).
     * @param type $mVar The variable.
     * @param integer $nDepth Optional The max depth of tree print.
     * @return string.
     */
    public static function getDumpVar(&$mVar, $nDepth = 10)
    {
        return self::getDumpIteration($mVar, $nDepth);
    }

    /**
     * It prints the human-readable data of the variable (the variable dump) with 
     * highlighted syntax.
     * @param mixed $mVar The variable.
     * @param integer $nDepth Optional The max depth of tree print.
     */
    public static function dumpVar(&$mVar, $nDepth = 10)
    {
        $sOut = self::getDumpVar($mVar, $nDepth);
        $sRaw = highlight_string("<?php\n{$sOut}", true);
        echo preg_replace('#&lt;\?php<br \/>#', '', $sRaw, 1);
    }
}
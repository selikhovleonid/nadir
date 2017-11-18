<?php

namespace nadir;

/**
 * This namespace contains developer tools.
 * @author Leonid Selikhov.
 */
/** @var integer The constant determines the count of spaces to indent. */
const SPACES_PER_TAB = 4;

/**
 * It returns of string representation of array at the current level of the
 * tree. It's recursive method.
 * @param mixed $mVar The variable.
 * @param int $nDepth Optional The max depth of tree print.
 * @param int $nLevel Optional The current level of the tree.
 * @param mixed[] $aObjects Optional The array of variable ojects.
 * @return string.
 */
function _getDumpArrayIteration(
    $mVar,
    $nDepth = 10,
    $nLevel = 0,
    array& $aObjects = array()
) {
    $sOut       = '';
    $sSpacesOut = str_repeat(' ', SPACES_PER_TAB * $nLevel);
    if ($nDepth <= $nLevel) {
        $sOut .= "array\n{$sSpacesOut}(...)";
    } elseif (empty($mVar)) {
        $sOut .= 'array()';
    } else {
        $sSpacesIn = $sSpacesOut.str_repeat(' ', SPACES_PER_TAB);
        $sOut      .= "array\n{$sSpacesOut}(";
        foreach ($mVar as $sKey => $mValue) {
            $sOut .= "\n{$sSpacesIn}"
                ._getDumpIteration($sKey, $nDepth, 0, $aObjects)
                .' => '
                ._getDumpIteration($mValue, $nDepth, $nLevel + 1, $aObjects);
        }
        $sOut .= "\n{$sSpacesOut})";
    }
    return $sOut;
}

/**
 * It returns the string representation of object at the current level of the
 * tree. It's recursive method.
 * @param mixed $mVar The variable.
 * @param int $nDepth Optional The max depth of tree print.
 * @param int $nLevel Optional The current level of the tree.
 * @param mixed[] $aObjects Optional The array of variable ojects.
 * @return string.
 */
function _getDumpObjIteration(
    $mVar,
    $nDepth = 10,
    $nLevel = 0,
    array& $aObjects = array()
) {
    $sOut       = '';
    $sClassName = get_class($mVar);
    $sSpacesOut = str_repeat(' ', SPACES_PER_TAB * $nLevel);
    if (($iObj       = array_search($mVar, $aObjects, true)) !== false) {
        $sOut .= "{$sClassName} object #"
            .($iObj + 1)
            ."\n{$sSpacesOut}(...)";
    } elseif ($nDepth <= $nLevel) {
        $sOut .= "{$sClassName} object"
            ."\n{$sSpacesOut}(...)";
    } else {
        // Возвращает модификаторы свойств объекта.
        $funcGetPropMod = function (\ReflectionProperty $oProp) {
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
        $sSpacesIn   = $sSpacesOut.str_repeat(' ', SPACES_PER_TAB);
        $oReflection = new \ReflectionClass($sClassName);
        $aProps      = $oReflection->getProperties();
        $sOut        .= "{$sClassName} object #{$iObj}\n{$sSpacesOut}(";
        foreach ($aProps as $oProp) {
            $oProp->setAccessible(true);
            $sOut .= "\n{$sSpacesIn}"
                .'['
                ._getDumpIteration($oProp->getName(), $nDepth, 0, $aObjects)
                .':'.$funcGetPropMod($oProp)
                .'] => '
                ._getDumpIteration(
                    $oProp->getValue($mVar),
                    $nDepth,
                    $nLevel + 1,
                    $aObjects
                );
            if (!$oProp->isPublic()) {
                $oProp->setAccessible(false);
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
 * @param mixed $mVar The variable.
 * @param int $nDepth Optional The max depth of tree print.
 * @param int $nLevel Optional The current level of the tree.
 * @param mixed[] $aObjects Optional The array of variable ojects.
 * @return string.
 */
function _getDumpIteration(
    $mVar,
    $nDepth = 10,
    $nLevel = 0,
    array& $aObjects = array()
) {
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
            $sOut .= _getDumpArrayIteration($mVar, $nDepth, $nLevel, $aObjects);
            break;
        case 'object':
            $sOut .= _getDumpObjIteration($mVar, $nDepth, $nLevel, $aObjects);
            break;
        default:
            break;
    }
    return $sOut;
}

/**
 * It returns the human-readable data of the variable (the variable dump).
 * @param mixed $mVar The variable.
 * @param integer $nDepth Optional The max depth of tree print.
 * @return string.
 */
function getDumpVar($mVar, $nDepth = 10)
{
    return _getDumpIteration($mVar, $nDepth);
}

/**
 * It prints the human-readable data of the variable (the variable dump) with
 * highlighted syntax.
 * @param mixed $mVar The variable.
 * @param integer $nDepth Optional The max depth of tree print.
 */
function dumpVar($mVar, $nDepth = 10)
{
    $sOut = getDumpVar($mVar, $nDepth);
    $sRaw = highlight_string("<?php\n{$sOut}", true);
    echo preg_replace('#&lt;\?php<br \/>#', '', $sRaw, 1);
}

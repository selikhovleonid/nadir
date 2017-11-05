<?php

namespace Nadir\Core;

/**
 * This's a factory class for generating of View-objects (View, Layout).
 * @author coon
 */
class ViewFactory
{

    /**
     * @ignore.
     */
    private function __construct()
    {
        // nothing here
    }

    /**
     * The method creates a view object assigned with the specific controller and 
     * action in it. If controller name is empty it means a markup file determined 
     * only with action name. It doesn't physically consist into the direcory named 
     * as controller, it's in the root of the view directory.
     * @param string $sCtrlName|null The controller name (as optional)
     * @param string $sActionName The action name.
     * @return \Nadir\Core\View|null It returns null if view file isn't readable.
     */
    public static function createView($sCtrlName = null, $sActionName)
    {
        $sViewsRoot = AppHelper::getInstance()->getComponentRoot('views');
        $sAddPath   = '';
        if (!empty($sCtrlName)) {
            $sAddPath .= DIRECTORY_SEPARATOR.strtolower($sCtrlName);
        }
        $sViewFile = $sViewsRoot.$sAddPath.DIRECTORY_SEPARATOR
            .strtolower($sActionName).'.php';
        if (is_readable($sViewFile)) {
            return new View($sViewFile);
        }
        return null;
    }

    /**
     * It creates a layout object.
     * @param type $sLayoutName The layout name.
     * @param Nadir\Core\View $oView The object of view.
     * @return \Nadir\Core\Layout|null
     */
    public static function createLayout($sLayoutName, View $oView)
    {
        $sLayoutsRoot = AppHelper::getInstance()->getComponentRoot('layouts');
        $sLayoutFile  = $sLayoutsRoot.DIRECTORY_SEPARATOR
            .strtolower($sLayoutName).'.php';
        if (is_readable($sLayoutFile)) {
            return new Layout($sLayoutFile, $oView);
        }
        return null;
    }

    /**
     * The method creates a snippet-object.
     * @param type $sSnptName The snippet name.
     * @return \Nadir\Core\Snippet|null.
     */
    public static function createSnippet($sSnptName)
    {
        $sSnptRoot = AppHelper::getInstance()->getComponentRoot('snippets');
        $SnptFile  = $sSnptRoot.DIRECTORY_SEPARATOR
            .strtolower($sSnptName).'.php';
        if (is_readable($SnptFile)) {
            return new Snippet($SnptFile);
        }
        return null;
    }
}
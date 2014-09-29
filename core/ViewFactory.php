<?php

/**
 * Класс-фабрика для генрации объектов представления (View, Layout).
 * @author coon
 */

namespace core;

class ViewFactory {

    /**
     * @ignore.
     */
    private function __construct() {
        // nothing here
    }

    /**
     * Метод создает объект представления, связанный с определенным контроллером
     * и action. При пустом имени контроллера считается, что файл разметки
     * определяется только именем action, т.е. физически расположен не в 
     * директории с именем контроллера, а в корне директории с представлениями.
     * @param string $sCtrlName|null Имя контроллера.
     * @param string $sActionName Имя action.
     * @return \core\View|null
     */
    public static function createView($sCtrlName = NULL, $sActionName) {
        $sViewsRoot = AppHelper::getInstance()->getComponentRoot('views');
        $sAddPath   = '';
        if (!empty($sCtrlName)) {
            $sAddPath .= DIRECTORY_SEPARATOR . strtolower($sCtrlName);
        }
        $sViewFile = $sViewsRoot
                . $sAddPath
                . DIRECTORY_SEPARATOR . strtolower($sActionName) . '.php';
        if (is_readable($sViewFile)) {
            return new View($sViewFile);
        } else {
            return NULL;
        }
    }

    /**
     * Создает объект макета.
     * @param type $sLayoutName Имя макета.
     * @param \core\View $oView Объект представления.
     * @return \core\Layout|null
     */
    public static function createLayout($sLayoutName, View $oView) {
        $sLayoutsRoot = AppHelper::getInstance()->getComponentRoot('layouts');
        $sLayoutFile  = $sLayoutsRoot . DIRECTORY_SEPARATOR
                . strtolower($sLayoutName) . '.php';
        if (is_readable($sLayoutFile)) {
            return new Layout($sLayoutFile, $oView);
        } else {
            return NULL;
        }
    }

}

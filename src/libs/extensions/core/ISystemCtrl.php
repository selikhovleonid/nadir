<?php

namespace extensions\core;

/**
 * Интерфейс системного контроллера.
 * @author coon
 */
interface ISystemCtrl {

    /**
     * Action отвечает за генерацию страницы с ошибкой 401.
     * @param array $aErrors Массив внутренних ошибок.
     */
    public function actionPage401(array $aErrors);

    /**
     * Action отвечает за генерацию страницы с ошибкой 403.
     * @param array $aErrors Массив внутренних ошибок.
     */
    public function actionPage403(array $aErrors);

    /**
     * Action отвечает за генерацию страницы с ошибкой 404.
     * @param array $aErrors Массив внутренних ошибок.
     */
    public function actionPage404(array $aErrors);
}

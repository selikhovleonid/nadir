<?php

namespace nadir\extensions\core;

/**
 * The interface of system controller.
 * @author coon
 */
interface SystemCtrlInterface
{

    /**
     * This action contains functionality for 401 error page generating.
     * @param array $aErrors The array of internal errors.
     */
    public function actionPage401(array $aErrors);

    /**
     * This action contains functionality for 403 error page generating.
     * @param array $aErrors The array of internal errors.
     */
    public function actionPage403(array $aErrors);

    /**
     * This action contains functionality for 404 error page generating.
     * @param array $aErrors The array of internal errors.
     */
    public function actionPage404(array $aErrors);
}
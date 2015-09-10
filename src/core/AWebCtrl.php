<?php

/**
 * Абстрактный класс веб-контроллера. Несмотря на то, что ни один метод не объявлен
 * как абстрактный, модификатор abstract указан намеренно, чтобы исключить
 * возможность создания экземпляра этого класса.
 * @author coon
 */

namespace core;

abstract class AWebCtrl {

    /** @var \core\Request Объект запроса. */
    protected $request = NULL;

    /** @var \core\View Объект представления. */
    protected $view = NULL;

    /** @var \core\Layout Объект макета. */
    protected $layout = NULL;

    /**
     * Связывает объект с объектом запроса и, возможно, объектом представления 
     * (полного или частичного).
     * @param \core\Request $oRequest.
     * @param \core\AView|null $oView.
     */
    public function __construct(Request $oRequest, AView $oView = NULL) {
        $this->request = $oRequest;
        if (!is_null($oView)) {
            if ($oView instanceof View) {
                $this->view = $oView;
            } elseif ($oView instanceof Layout) {
                $this->layout = $oView;
                $this->view   = $this->layout->view;
            }
        }
    }

    /**
     * Возвращает объект связанного запроса.
     * @return \core\Request|null.
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Возвращает объект ассоциированного представления.
     * @return \core\View|null.
     */
    protected function getView() {
        return $this->view;
    }

    /**
     * Служит для связывания контроллера с представлением (как с умалчиваемым,
     * так и с соответствующим другому контроллеру).
     * @param string $sCtrlName Имя контроллера.
     * @param string $sActionName Имя действия (без префикса action).
     * @return void.
     */
    protected function setView($sCtrlName, $sActionName) {
        $this->view = ViewFactory::createView($sCtrlName, $sActionName);
        if (!is_null($this->layout)) {
            $this->layout->view = $this->view;
        }
    }

    /**
     * Возвращает объект ассоциированного макета.
     * @return \core\Layout|null.
     */
    protected function getLayout() {
        return $this->layout;
    }

    /**
     * Связывает контроллер с макетом.
     * @param string $sLayoutName Имя макета.
     * @return void.
     * @throws Exception.
     */
    protected function setLayout($sLayoutName) {
        if (!is_null($this->view)) {
            $this->layout = ViewFactory::createLayout($sLayoutName, $this->view);
        } else {
            throw new Exception('Unable set Layout without View.');
        }
    }

    /**
     * Осуществляе рендеринг страницы, как полный (макет и представление), так и
     * частичный (представление).
     * @return void.
     * @throws Exception.
     */
    protected function render() {
        if (!is_null($this->layout)) {
            $this->layout->render();
        } elseif (!is_null($this->view)) {
            $this->partialRender();
        } else {
            throw new Exception('Unable render with empty View.');
        }
    }

    /**
     * Метод осуществляет частичный рендеринг (одного представления, без макета).
     * @return void.
     */
    protected function partialRender() {
        $this->view->render();
    }

    /**
     * Метод преобразует заэкранированные юникод-символы в неэкранированные.
     * @param string $sData Входная строка.
     * @return string.
     */
    private static function _unescapeUnicode($sData) {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function (array & $aMatches) {
            $sSym = mb_convert_encoding(pack('H*', $aMatches[1]), 'UTF-8', 'UTF-16');
            return $sSym;
        }, $sData);
    }

    /**
     * Рендерит страницу с данными в JSON-формате.
     * @param mixed $mData Входные данные.
     * @return void.
     */
    protected function renderJson($mData) {
        echo self::_unescapeUnicode(json_encode($mData));
    }

    /**
     * Метод осуществляет редирект (перенаправление) по URL, указанному в параметре.
     * По умолчанию HTTP-код возвращаемой страницы - 302. Метод безусловно
     * завершает выполнение скрипта, код указанный после него, выполнен не будет.
     * @param string $sUrl
     * @param bool $fIsPermanent Флаг признака постоянного (permanent) редиректа.
     * @return void.
     */
    protected function redirect($sUrl, $fIsPermanent = FALSE) {
        $nCode = $fIsPermanent ? 301 : 302;
        Headers::getInstance()
                ->addByHttpCode($nCode)
                ->add('Location: ' . $sUrl)
                ->run();
        exit;
    }

}

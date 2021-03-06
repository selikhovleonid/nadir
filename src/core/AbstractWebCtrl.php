<?php

namespace nadir\core;

/**
 * This is the class of abstract web-controller. Despite the fact that no one method
 * declared as abstract, the 'abstract' modifier is set a specially to exclude the
 * possibility of creating an instance of the class.
 * @author Leonid Selikhov
 */
abstract class AbstractWebCtrl
{
    /** @var \nadir\core\Request The request object. */
    protected $request = null;

    /** @var \nadir\core\View The view object. */
    protected $view = null;

    /** @var \nadir\core\Layout The layout object. */
    protected $layout = null;

    /**
     * The constructor assigns object with request object and possibly with
     * the view object (full or partial).
     * @param \nadir\core\Request $oRequest The request object.
     * @param \nadir\core\AbstractView|null $oView The view object.
     */
    public function __construct(Request $oRequest, AbstractView $oView = null)
    {
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
     * It returns the object of assigned request.
     * @return \nadir\core\Request|null.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * It returns the object of associated view.
     * @return \nadir\core\View|null.
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * It used for binding the controller with a view (both with the default
     * and with corresponding another controller).
     * @param string $sCtrlName The controller name.
     * @param string $sActionName The action name (without prefix action).
     * @return void.
     */
    protected function setView($sCtrlName, $sActionName)
    {
        $this->view = ViewFactory::createView($sCtrlName, $sActionName);
        if (!is_null($this->layout)) {
            $this->layout->view = $this->view;
        }
    }

    /**
     * It returns the object of associated layout.
     * @return \nadir\core\Layout|null.
     */
    protected function getLayout()
    {
        return $this->layout;
    }

    /**
     * It assigns the controller with layout.
     * @param string $sLayoutName The layout name.
     * @return void.
     * @throws Exception.
     */
    protected function setLayout($sLayoutName)
    {
        if (is_null($this->view)) {
            throw new Exception("It's unable to set Layout without View.");
        }
        $this->layout = ViewFactory::createLayout($sLayoutName, $this->view);
    }

    /**
     * It renders the page both full (layout with view) and partial (view only).
     * @return void.
     * @throws Exception.
     */
    protected function render()
    {
        if (!is_null($this->layout)) {
            $this->layout->render();
        } elseif (!is_null($this->view)) {
            $this->partialRender();
        } else {
            throw new Exception("It's unable to render with empty View.");
        }
    }

    /**
     * The method provides partial rendering (view without layout).
     * @return void.
     */
    protected function partialRender()
    {
        $this->view->render();
    }

    /**
     * The method converts escaped Unicode chars to unescaped.
     * @param string $sData The input string.
     * @return string.
     */
    private static function unescapeUnicode($sData)
    {
        return preg_replace_callback(
            '/\\\\u([0-9a-f]{4})/i',
            function (array & $aMatches) {
                $sSym = mb_convert_encoding(
                    pack('H*', $aMatches[1]),
                    'UTF-8',
                    'UTF-16'
                );
                return $sSym;
            },
            $sData
        );
    }

    /**
     * It renders the page with JSON-formatted data.
     * @param mixed $mData The input data.
     * @return void.
     */
    protected function renderJson($mData)
    {
        echo self::unescapeUnicode(json_encode($mData));
    }

    /**
     * The method redirects to the URL, which passed as param. The HTTP-code is
     * 302 as default. The method unconditional completes the script execution,
     * the code after it will not be executed.
     * @param string $sUrl
     * @param bool $fIsPermanent The flag of permanent redirect.
     * @return void.
     */
    protected function redirect($sUrl, $fIsPermanent = false)
    {
        $nCode = $fIsPermanent ? 301 : 302;
        Headers::getInstance()
            ->addByHttpCode($nCode)
            ->add('Location: '.$sUrl)
            ->run();
        exit;
    }
}

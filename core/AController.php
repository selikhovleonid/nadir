<?php

/**
 * Абстрактный класс контроллера. Несмотря на то, что ни один метод не объявлен
 * как абстрактный, модификатор abstract указан намеренно, чтобы исключить
 * возможность создания экземпляра этого класса.
 * @author coon
 */

namespace core;

abstract class AController {

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
				$this->layout	 = $oView;
				$this->view		 = $this->layout->view;
			}
		}
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
	 * Рендерит страницу с ошибкой 404.
	 * @return void.
	 */
	protected function render404() {
		$sRawName	 = AppHelper::getInstance()->getConfig('page404');
		Headers::getInstance()->addByHttpCode(404)->run();
		$this->view	 = ViewFactory::createView(NULL, $sRawName);
		$this->view->render();
	}

	/**
	 * Рендерит страницу с данными в JSON-формате.
	 * @param mixed $mData Входные данные.
	 * @return void.
	 */
	protected function renderJson($mData) {
		echo stripslashes(json_encode($mData));
	}

}
<?php

/**
 * Абстрактный класс представления.
 * @author coon
 */

namespace core;

abstract class AView extends AUserPropAccessor {

	/** @var string Путь к файлу с разметкой представления. */
	protected $filePath = '';

	/**
	 * Инициализация приватных свойств объекта.
	 * @param string $sViewFilePath Путь к файлу с разметкой представления.
	 * @return self.
	 */
	public function __construct($sViewFilePath) {
		$this->setFilePath($sViewFilePath);
	}
	
	/**
	 * Метод-акссесор к переменной, содержащей путь к файлу представления.
	 * @return string.
	 */
	public function getFilePath() {
		return $this->filePath;
	}
	
	/**
	 * Связывает объект с файлом представления.
	 * @param string $sViewFilePath Путь к файлу с разметкой представления.
	 * @throws Exception.
	 */
	public function setFilePath($sViewFilePath) {
		if (is_readable($sViewFilePath)) {
			$this->filePath = $sViewFilePath;
		} else {
			throw new Exception("View file {$sViewFilePath} isn't readable");
		}		
	}

	/**
	 * Абстрактный метод, отвечающий за рендеринг файла представления.
	 * @return void.
	 */
	abstract public function render();
}

<?php

/**
 * Интерфейс описывает функциональность запускаемого и останавливаемого 
 * процесса.
 * @author coon
 */

namespace core;

interface IProcess extends IRunnable {

	/**
	 * Останавливает и/или уничтожает процесс.
	 * @return void.
	 */
	public function stop();
}


<?php

/**
 * Description of View
 *
 * @author coon
 */

namespace core;

class View extends AView {

	public function render() {
		require_once($this->filePath);
	}

}


<?php

/**
 * Тестовый класс модели. Демонстрационный вариант.
 * @author coon
 */

namespace models;

use extensions\core\AModel;

class Test extends AModel {

    public function readDefault() {
        return array(
            'foo' => 'bar',
            'bar' => array(42, 'baz')
        );
    }

}

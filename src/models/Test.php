<?php
/**
 * This's demo version of model class.
 * @author coon
 */

namespace models;

use extensions\core\AbstractModel;

class Test extends AbstractModel
{

    public function readDefault()
    {
        return array(
            'foo' => 'bar',
            'bar' => array(42, 'baz')
        );
    }
}
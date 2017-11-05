<?php
/**
 * This's demo version of model class.
 * @author coon
 */

namespace Nadir\Models;

use Nadir\Extensions\Core\AbstractModel;

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
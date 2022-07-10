<?php

declare(strict_types=1);

/**
 * @author Hi Dawnc
 * @date   2022-05-21
 */

namespace Wms\Test;

class TestPo
{

    public function __set($name, $val)
    {
        $this->$name = $val;
    }

    public function __get($name)
    {
        return $this->$name;
    }

}

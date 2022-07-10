<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-10
 */

namespace Wms\Test\app\Shell;

use Wms\Fw\Shell;

class Test extends Shell
{
    protected function handle(?array $param = null): void
    {
        var_dump($param);
    }

}

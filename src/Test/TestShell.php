<?php

declare(strict_types=1);

/**
 * @author Hi Developer
 * @date   2022-05-15
 */

namespace Wms\Test;

use Wms\Lib\ShellHandle;

class TestShell extends ShellHandle
{
    protected function handle($param = null)
    {

        $cls = new TestPdo();
        $cls->testQuery();
        //$cls->test1();
        //$cls->test2();
        //$cls->testException();
        //$cls->test3();


    }

}

<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-04
 */

namespace Wms\Test\App\Control;

use Wms\Fw\WDb;

class PortalControl
{
    public function index($p1 = '', $p2 = '', $p3 = '')
    {
        $r = WDb::getData("show tables");
        //return (new Response())->withContent("xxxx");
        return $r;
    }
}

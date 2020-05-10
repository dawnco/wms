<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

namespace app\control;


use wms\fw\Db;

class Portal
{

    public function index()
    {
        echo "hello";
        $db   = Db::instance();
        $data = $db->getData("show tables");
    }
}

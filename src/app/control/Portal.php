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
        $db   = Db::instance();
        $data = $db->getData("show tables");
        var_dump($data);
    }

    public function test($param)
    {
        var_dump($param);
    }
}

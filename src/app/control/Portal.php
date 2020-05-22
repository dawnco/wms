<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

namespace app\control;

use helper\Model;
use wms\fw\Db;

class Portal
{

    public function index()
    {
        $db   = Db::instance();
        $data = $db->getData("show tables");
        $data = $db->getData("SELECT * FROM brands WHERE id IN (?)", [['abc', 2, 3]]);
        $data = $db->getData("SELECT * FROM brands WHERE id IN (?)", [['abc', 2, 3]]);
        $data = $db->getData("SELECT * FROM brands WHERE id IN (?)", [['abc', 2, 3]]);

        $m  = Model::instance('brands');
        $d  = $m->delete(526, ['name' => '1212']);
        $id = $m->create(['name' => 'n1']);
        //$m->update($id, ['name' => 'n2']);
        $m->delete($id, ['name' => 'n1']);
        //$m->restore($id, ['name' => 'n1']);
        $m->all();
        var_dump($db->sql);
    }

    public function test($param)
    {
        var_dump($param);
    }
}

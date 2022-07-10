<?php

declare(strict_types=1);

/**
 * @author Hi Dawnc
 * @date   2022-05-21
 */

namespace Wms\Test;

use Wms\Connection\WDb;
use Wms\Database\QueryBuilder;

class TestPdo
{
    protected $db;

    public function __construct()
    {
        $this->db = WDb::connection();
    }

    public function testQuery()
    {

        $query = QueryBuilder::query("SELECT id FROM test");
        $query->where("AND id > ?", 0, true);
        $query->where("AND id < ?", 120, true);
        $query->where("AND (id < ? OR id > ?)", [100, 200], true);
        $query->where("AND (id <= ? AND id >= ?)", [24, 24], true);
        $query->limit(0, 2);
        //$query->order("id DESC");
        [$sql, $params] = $query->getQuery();
        $d = WDb::getData($sql, $params);
        var_dump($d);
    }

    public function test1()
    {

        $in = [];
        $r = WDb::insert('test', ['name' => "name1", 'note' => 'note']);
        echo "insert id $r\n";
        $r = WDb::replace('test', ['name' => "name1", 'note' => 'note']);
        echo "insert id $r\n";
        $r = WDb::insertGetId('test', ['name' => "name1", 'note' => 'note']);
        echo "insert id $r\n";
        $in[] = $r;
        $r = WDb::update('test', ['name' => "name1", 'note' => 'note11'], ['name' => "name1"]);
        echo "update rows $r\n";
        $r = WDb::insert('test', ['name' => "name3", 'note' => 'note22']);
        echo "insert id -- $r\n";
        $in[] = $r;

        WDb::insertBatch('test',
            [['name' => "name2", 'note' => 'note33'], ['name' => "name2", 'note' => 'note33']]);
        echo "delete rows $r\n";

        $r = WDb::delete('test', ['name' => 'name1']);
        echo "delete rows $r\n";
        $r = WDb::getLine('SELECT * FROM test WHERE name = ?', ['name2'], TestPo::class);
        echo "getLine rows " . json_encode($r) . "\n";

        /**
         * @var $r TestPo
         */
        $data = WDb::getData('SELECT * FROM test WHERE name = ?', ['name2'], TestPo::class);
        echo "getData rows " . json_encode($r) . "\n";

        foreach ($data as $r) {
            echo $r->id, " ", $r->id, " ", $r->note, "\n";
        }

    }

    public function test2()
    {
        $in = [];
        $r = $this->db->insert('test', ['name' => "name1", 'note' => 'note']);
        echo "insert id $r\n";
        $in[] = $r;
        $r = $this->db->update('test', ['name' => "name1", 'note' => 'note11'], ['name' => "name1"]);
        echo "update rows $r\n";
        $r = $this->db->insert('test', ['name' => "name3", 'note' => 'note22']);
        echo "insert id -- $r\n";
        $in[] = $r;

        $this->db->insertBatch('test',
            [['name' => "name2", 'note' => 'note33'], ['name' => "name2", 'note' => 'note33']]);
        echo "delete rows $r\n";

        $r = $this->db->delete('test', ['name' => 'name1']);
        echo "delete rows $r\n";
        $r = $this->db->getLine('SELECT * FROM test WHERE name = ?', ['name2']);
        echo "getLine rows " . json_encode($r) . "\n";

        $r = $this->db->getData('SELECT * FROM test WHERE name = ?', ['name2']);
        echo "getData rows " . json_encode($r) . "\n";


        $r = $this->db->getData('SELECT * FROM test WHERE name LIKE ?', ['%n%']);
        echo "getData rows like " . json_encode($r) . "\n";

    }

    public function test3()
    {
        $a = $this->db->getLine("SELECT * from test where name = ?", ['name3'], TestPo::class);
    }

    public function testException()
    {
        try {
            $this->db->getData("select a 1");
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
        }


        try {
            $this->db->getLine("select a 1");
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
        }

        try {
            $this->db->insert("test", ['id' => 1]);
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
        }

        var_dump($this->db->sql);
    }
}


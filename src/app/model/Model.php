<?php
/**
 * @author Dawnc
 * @date   2020-05-22
 */

namespace app\model;

use wms\fw\Db;

class Model
{
    protected $table     = '';
    protected $sortField = 'sort_num';

    protected $updatedAt = 'updated_at';
    protected $createdAt = 'created_at';
    protected $deletedAt = 'deleted_at';

    // 是否启用
    protected $enabledField = 'enabled';

    protected $db = null;

    public function __construct($conf = 'default')
    {
        $this->db = Db::instance($conf);
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public static function instance($table = null, $conf = 'default')
    {
        $cls = new self($conf);
        if ($table) {
            $cls->setTable($table);
        }
        return $cls;
    }

    public function count($where = [])
    {
        $sql_where = $this->where($where);
        return $this->getVar("SELECT count(*) FROM `$this->table` WHERE " . $sql_where);
    }

    public function all($where = [], $page = 1, $size = 10, $order = "id DESC", $fields = '*')
    {
        $size      = abs($size) ?: 1;
        $start     = abs(($page ?: 1) - 1) * $size;
        $sql_where = $this->where($where);
        $query     = "SELECT {$fields} FROM `$this->table` WHERE $sql_where ORDER BY $order LIMIT $start, $size";
        return $this->db->getData($query);
    }

    public function find($id)
    {
        $where[]   = ['AND id = ?', $id, true];
        $sql_where = $this->where($where);
        return $this->db->getLine('SELECT * FROM `' . $this->table . '` WHERE ' . $sql_where);
    }

    public function update($id, $data)
    {
        $data[$this->updatedAt] = $this->timestamp();
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function create($data)
    {
        $data[$this->createdAt]    = $this->timestamp();
        $data[$this->enabledField] = 0;
        return $this->db->insert($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->query("UPDATE {$this->table} SET `{$this->deletedAt}` = ? WHERE id = ?", [$this->timestamp(), $id]);
    }

    protected function realDelete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    protected function timestamp()
    {
        return date('Y-m-d H:i:s');
    }

    protected function where($where)
    {
        $where[]   = ["AND `$this->enabledField` = 1", true];
        $sql_where = $this->db->where($where);
        return $sql_where;
    }

}

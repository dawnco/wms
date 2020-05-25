<?php
/**
 * @author Dawnc
 * @date   2020-05-09
 */

namespace wms\database;

interface IDatabase
{
    /**
     * 获取一个值
     * @param string $query
     * @param array  $bind 预定义参数
     */
    public function getVar($query, $bind = null);

    /**
     * 获取一行数据
     * @param string $query
     * @param array  $bind 预定义参数
     */
    public function getLine($query, $bind = null);

    /**
     * 获取数据
     * @param string $query
     * @param array  $bind 预定义参数
     * @return array
     */
    public function getData($query, $bind = null);

    /**
     * 简单分页数据
     * @param string $table
     * @param array  $where
     * @param int    $page
     * @param int    $size
     * @param string $order
     * @param string $fields
     * @return array
     * @author  Dawnc
     */
    public function getPageData($table, $where = [], $page = 1, $size = 15, $order = "id DESC", $fields = '*');

    /**
     * 快捷查询
     * @param string $table
     * @param string $value
     * @param string $index
     * @param string $field
     */
    public function getLineBy($table, $value, $index = "id", $field = "*");

    /**
     * 快捷查询
     * @param string $table
     * @param array  $where 数组
     * @param string $field
     * @return mixed
     * @author  Dawnc
     */
    public function getLineByWhere($table, $where, $field = "*");

    public function getDataByWhere($table, $where, $field = "*");


    /**
     * 插入
     * @param string $table
     * @param array  $data
     * @return type
     */
    public function insert($table, $data);

    /**
     * 更新或者添加一条数据
     * @param string $table
     * @param array  $data
     * @param string $value
     * @param string $field
     * @return type
     */
    public function upsert($table, $data, $value, $field = "id");

    /**
     * 批量插入
     * @param string $table
     * @param array  $data
     */
    public function insertBatch($table, $data);

    /**
     * 批量插入(忽略重复索引)
     * @param string $table
     * @param array  $data
     */
    public function insertIgnoreBatch($table, $data);

    /**
     * 更新sql
     * @param string $table
     * @param array  $data
     * @param mix    $where 数组 或者 字符串  字符串则表示ID
     * @return type
     */
    public function update($table, $data, $where);

    /**
     * 删除
     * @param string           $table 表名
     * @param string|array|int $where 条件 或者 字符串  字符串则表示ID
     */
    public function delete($table, $where);

    /**
     * 转义安全字符
     * @param string $val
     * @return string
     */
    public function escape($val);

    /**
     * 执行sql
     * @param type $query
     * @param type $bind
     * @return boolean
     */
    public function query($query, $bind = null);

    /**
     * 开始事物
     */
    public function begin();

    /**
     * 提交
     */
    public function commit();

    /**
     * 回滚
     */
    public function rollback();
}

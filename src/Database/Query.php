<?php
/**
 * @author Dawnco
 * @date   2022-05-21
 */

namespace Wms\Database;

class Query
{


    protected string $sql = '';
    protected array $where = [];
    protected int $start = 0;
    protected int $limit = 0;
    protected string $order = '';


    public function __construct(string $sql)
    {
        $this->setSql($sql);
    }

    public function setSql(string $sql)
    {
        $this->sql = $sql;
    }


    public function getQuery()
    {
        [$wSql, $params] = QueryBuilder::where($this->where);
        $sql = $this->sql . $wSql;

        if ($this->order) {
            $sql .= " ORDER BY " . $this->order;
        }

        if ($this->start || $this->limit) {
            // 处理 LIMIT ? 的问题
            $sql .= " LIMIT $this->start, $this->limit";
        }

        return [$sql, $params];
    }


    /**
     * @param string $sql       条件语句 比如   AND name  = ?
     * @param mixed  $val       绑定的值 条件语句 条件语句里多个问好 这里用数组
     * @param mixed  $condition 是否使用条件 为 null是 使用 $val 做条件
     * @return Query
     */
    public function where(string $sql, $val, $condition = null): Query
    {
        $this->where[] = [$sql, $val, $condition === null ? (bool)$val : (bool)$condition];
        return $this;
    }

    /**
     * 设置排序
     * @param string $order e.g. id desc
     * @return Query
     */
    public function order(string $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * 设置偏移量
     * @param int $start
     * @param int $limit
     * @return Query
     */
    public function limit(int $start, int $limit)
    {
        $this->start = $start;
        $this->limit = $limit;
        return $this;
    }

}

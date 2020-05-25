<?php
/**
 * @author Dawnc
 * @date   2020-05-09
 */

namespace wms\database;


use wms\fw\Exception;

class Database
{

    /**
     * 根据条件拼接sql where片段
     * 主要解决前台可选一项或多项条件进行查询时的sql拼接
     * 拼接规则：
     * 0 =>sql，必须，sql片段
     * 1 =>值缩写，必须，sql片段中要填充的值
     * 2 =>条件，选填，默认判断不为空，如果设置了条件则用所设置的条件
     * $factor_list = [
     *        ['and a.id=?i', 12 ],
     *        ['and a.name like '%?p', 'xin'],
     *        ['and a.age > ?i', 18],
     *        ['or (a.time > ?s and a.time < ?s )', ['2014', '2015'], 1==1 ]
     * ];
     * @param array $factor_list
     * @return string
     */
    public function where($factor_list)
    {
        $where_sql = ' 1=1';
        foreach ($factor_list as $factor) {
            $condition = $factor[2] ?? $factor[1];
            if ($condition) {
                $where_sql .= " " . $this->prepare($factor[0], [$factor[1]]);
            }
        }
        return $where_sql;
    }

    /**
     * 预编译sql语句 ?i = 表示int
     *              ?s 和 ? 字符串
     *              ?p 原始sql
     *              ?lr = like 'str%' ?ll = like '%str' l = like '%str%'
     *              sql id IN (1,2 3) 用法   ("id IN (?)", [[1,2,3]]);
     * @param string       $query
     * @param array|string $data
     * @return string
     */
    public function prepare($sql, $data = null)
    {


        if ($data === null) {
            return $sql;
        } elseif (!is_array($data)) {
            throw new Exception("except array data: $sql");
        }

        $sql = str_replace(
            array('?lr', '?ll', '?l', '?i', '?s', '?p', '?'),
            array('"%s%%"', '"%%%s"', '"%%%s%%"', '%d', '"%s"', '%s', '"%s"'),
            $sql);

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->valIn($v);
            } else {
                $data[$k] = $this->escape($v);
            }
        }

        return vsprintf($sql, $data);
    }


    /**
     * 转义 用于 sql in 查询
     * @param        $array
     * @param string $type
     * @return string
     */
    protected function valIn($array)
    {
        foreach ($array as $k => $v) {
            $array[$k] = $this->escape($v);
        }
        return implode('","', $array);
    }

}

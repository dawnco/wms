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
     * 's'=>sql，必须，sql片段
     * 'v'=>值缩写，必须，sql片段中要填充的值
     * 'c'=>条件，选填，默认判断不为空，如果设置了条件则用所设置的条件
     * $factor_list = array(
     *        array('s'=>'and a.id=?i', 'v'=>12 ),
     *        array('s'=>"and a.name like '%?p'", 'v'=>'xin'),
     *        array('s'=>'and a.age > ?i', 'v'=>18),
     *        array('s'=>'or (a.time > ?s and a.time < ?s )', 'v'=>array('2014', '2015'), 'c'=>(1==1) )
     * );
     * @param array $factor_list
     * @return string
     */
    public function where($factor_list)
    {
        $where_sql = ' 1=1';
        foreach ($factor_list as $factor) {
            $condition = isset($factor['c']) ? $factor['c'] : $factor['v'];
            if ($condition) {
                $where_sql .= " " . $this->prepare($factor['s'], $factor['v']);
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
            throw new Exception("except array data");
        }

        $sql = str_replace(
            array('?lr', '?ll', '?l', '?i', '?s', '?p', '?'),
            array('"%s%%"', '"%%%s"', '"%%%s%%"', '%d', '"%s"', '%s', '"%s"'),
            $sql);

        foreach ($data as $k => $v) {
            $data[$k] = $this->escape($v);
        }
        return vsprintf($query, $data);
    }

}

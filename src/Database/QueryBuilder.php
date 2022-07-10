<?php

/**
 * @author Dawnco
 * @date   2022-05-21
 */

namespace Wms\Database;

class QueryBuilder
{

    /**
     * 生成查询类
     * @param string $query
     * @return Query
     */
    public static function query(string $query): Query
    {
        return new Query($query);
    }

    /**
     * 生成占位符 sql 和 绑定参数
     * @param array $where [['sql like AND a = ?', 'val or condition', 'condition']]
     *                     [['sql like AND a = ?', ['val or condition'], 'condition']]
     * @return array ['sql'=>'', 'params'=>[]]
     */
    public static function where(array $where): array
    {
        $sql = [
            " WHERE 1=1 ",
        ];
        $params = [];
        foreach ($where as $c) {
            $s = $c[0];
            $p = $c[1];
            $condition = (bool)($c[2] ?? $c[1]);
            if ($condition) {
                $sql[] = $s;
                self::fillParams($params, $p);
            }
        }

        return [implode(" ", $sql), $params];
    }

    /**
     * 填充 ? 占位符
     * @param array $arr
     * @return string
     */
    public static function fillHolder(array $arr): string
    {
        return implode(",", array_fill(0, count($arr), "?"));
    }

    public static function fillParams(array &$params, $arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $v) {
                $params[] = $v;
            }
        } else {
            $params[] = $arr;
        }
    }


}

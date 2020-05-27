<?php
/**
 * @author Dawnc
 * @date   2020-05-15
 */

namespace app\dict;


class ProductStatus extends Dict
{
    const  OK    = 1;
    const  PAUSE = 2;
    const  STOP  = 3;
    const  OFF   = 4;

    protected static $data = [
        1 => '正常',
        2 => '当日暂停',
        3 => '停止发布',
        4 => '已下架',
    ];
}

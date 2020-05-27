<?php
/**
 * @author Dawnc
 * @date   2020-05-15
 */

namespace app\dict;


class Enabled extends Dict
{
    const NO  = 1;
    const YES = 2;

    protected static $data = [
        1 => '否',
        2 => '是',
    ];

    public static function states()
    {
        return [
            'on'  => ['value' => Enabled::YES, 'text' => '是', 'color' => 'success'],
            'off' => ['value' => Enabled::NO, 'text' => '否', 'color' => 'danger'],
        ];
    }
}

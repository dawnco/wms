<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

return [
    // 'url 可用正则' => ['c' => '控制器', 'm' => '方法'];
    "portal"    => ["c" => \app\control\Portal::class, "m" => "index"],
    "test/(.*)" => ["c" => "app\\control\\Portal", "m" => "test"],
];

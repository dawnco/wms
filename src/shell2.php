<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'app');
}

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__ . '/app');
}

if (!defined('WMS_PATH')) {
    define('WMS_PATH', __DIR__);
}

include WMS_PATH . "/fw/fn.php";
include WMS_PATH . "/fw/Fw.php";

$fw = new \wms\fw\Fw();
$fw->shell();

$db = \wms\fw\Db::instance();

$data = $db->getData("SELECT sheng,shi,qu,concat(cun, '|||',postcode) cun FROM addr");

$cls = new \wms\lib\TreeArr();
$arr = $cls->handle($data);

$batch = [];
foreach ($arr as $key => $v) {
    $name     = explode("|||", $v['name']);
    $postcode = $name[1] ?? "";
    $typeA    = [0 => "province", 1 => 'city', 2 => 'district', 3 => 'village'];

    $type = $typeA[substr_count($key, "^")];

    $batch[] = [
        "id"       => $v['id'],
        "pid"      => $v['pid'],
        "name"     => $name[0],
        "type"     => $type,
        "language" => "id",
        "postcode" => $postcode,
        //"n"        => $v['name'],
    ];
}

$a     = array_chunk($batch, 10000);
$index = 0;
$dbR = \wms\fw\Db::instance('dfhl');
foreach ($a as $b) {
    echo $index++;
    echo "\r\n";
    $dbR->insertBatch("sys_region", $b);
}

//var_dump(array_slice($batch, 0, 10));

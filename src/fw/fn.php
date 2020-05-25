<?php
/**
 * @author Dawnc
 * @date   2020-05-10
 */

/**
 * @param type $key
 * @param type $val_type 转换类型 i 整型  s 字符串  a 数组  默认不转换
 * @return type
 */
function input($key, $val_type = "s")
{
    $val = isset($_POST[$key]) ? $_POST[$key] :
        (isset($_GET[$key]) ? $_GET[$key] : false);


    switch ($val_type) {
        case "i":
            $val = (int)$val;
        break;
        case "a" :
            $val = $val ? (array)$val : array();
        break;
        default :
            $val = (string)$val;
            $val = trim($val);
        break;
    }

    return $val;
}

/**
 * 获取客户端ip
 * @return type
 */
function get_client_ip()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第?个非unknown的有效IP字符? */
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $realip);
    return !empty($realip[0]) ? $realip[0] : '0.0.0.0';
}


function dump(...$args)
{
    var_dump($args);
    exit;
}

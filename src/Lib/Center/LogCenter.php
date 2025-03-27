<?php

declare(strict_types=1);

/**
 * 日志客户端
 * @date   2025-03-26
 */

namespace Wms\Lib\Center;

use Wms\Fw\Log;

class LogCenter
{

    private static $stream = null;

    /**
     * @param string $store     那个store
     * @param array  $data      key和value的类型必须是字符串 格式 ["key"=>"value", "name"=>"jard", "age"=>"25"]
     * @param int    $timestamp 秒时间戳 默认当前秒
     * @return void
     */
    public static function record(
        string $store,
        array $data,
        int $timestamp = 0
    ): void {

        $kv = [];
        foreach ($data as $k => $v) {
            $kv[] = [
                "key" => $k,
                "val" => $v,
            ];
        }
        self::send(json_encode([
            "t" => $timestamp ?: time(), // 时间戳 秒
            "store" => $store, // 那个store
            "kv" => $kv
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected static function send(string $data): void
    {

        if (self::$stream == null) {
            self::$stream = stream_socket_client("udp://log.stat.com:8844", $errno, $error);
            if (!self::$stream) {
                return;
            }
            // https://www.php.net/manual/zh/function.stream-set-blocking.php
            stream_set_blocking(self::$stream, false);
        }
        $str = self::pack($data);
        if ($str && self::$stream) {
            fwrite(self::$stream, $str);
        }
    }

    protected static function pack(string $data): string
    {
        $length = strlen($data);
        $type = pack('n', 60);
        $remain = pack('C', 1);
        $size = pack('N', $length);

        if ($length > 10235) {
            Log::error("LogCenter 数据超出最大限制 $data");
            return "";
        }

        return $type . $remain . $size . $data;
    }


}

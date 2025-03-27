<?php

declare(strict_types=1);

/**
 * EC客户端
 * @date   2025-03-26
 */

namespace Wms\Lib\Center;

use Wms\Fw\Conf;

class EventCenter
{

    private static $stream = null;

    /**
     * @param string $name      事件名称
     * @param array  $params    ["_topic_"=>"指定写入那个kafka的 topic 默认 空 表示 event-center"]
     * @param string $requestId
     * @param int    $timestamp 毫秒时间戳 默认 当前毫秒
     * @param string $from      来自那个服务 默认取配置的  app_name
     * @return void
     */
    public static function app(
        string $name,
        array $params = [],
        string $requestId = "",
        int $timestamp = 0,
        string $topic = "",
        string $from = ""
    ): void {
        self::send(json_encode([
            "name" => $name,
            "_topic_" => $topic,
            "requestId" => $requestId,
            "from" => intval($from ?: Conf::get("app_name")),
            "country" => Conf::get("app_country") ?: "",
            "timestamp" => $timestamp ?: intval(microtime(true) * 1000),
            "params" => $params ?: null
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected static function send(string $data): void
    {

        if (self::$stream == null) {
            self::$stream = stream_socket_client("udp://center.stat.com:9820", $errno, $error);
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
        $type = pack('n', 55);
        $remain = pack('C', 1);
        $size = pack('n', $length);

        if ($length > 10235) {
            return "";
        }

        return $type . $remain . $size . $data;
    }


}

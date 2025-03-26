<?php

declare(strict_types=1);

/**
 * EC客户端
 * @date   2025-03-26
 */

namespace Wms\Lib\Center;

class EventCenter
{

    private static $stream = null;

    /**
     * @param string $from      来自那个服务
     * @param string $name      事件名称
     * @param int    $timestamp 毫秒时间戳
     * @param string $requestId
     * @param array  $params    ["_topic_"=>"指定写入那个kafka的 topic 默认 空 表示 event-center"]
     * @return void
     */
    public static function app(
        string $from,
        string $name,
        int $timestamp,
        string $requestId = "",
        array $params = []
    ): void {
        self::send(json_encode([
            "_topic_" => $params['_topic_'] ?? "",
            "requestId" => $requestId,
            "name" => $name,
            "from" => $from,
            "country" => "id",
            "timestamp" => $timestamp,
            "params" => $params ?: null
        ]));
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

<?php

declare(strict_types=1);

/**
 * 错误日志客户端
 * @date   2025-03-26
 */

namespace Wms\Lib\Center;

use Wms\Fw\Conf;

class ErrorCenter
{

    private static $stream = null;

    /**
     * @param array  $data      格式 查看 文档 
     * @return void
     */
    public static function record(array $data): void
    {

        if (!isset($data['file'])) {
            // 获取堆栈信息
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            // 获取调用该函数的文件和行号
            $callerFile = $backtrace[0]['file'];
            $callerLine = $backtrace[0]['line'];
            $data['file'] = $callerFile;
            $data['line'] = $callerLine;
        }

        $ms = arr_get($data, "t") ?: intval(microtime(true) * 1000);
        $p = [
            "t" => strval($ms),
            "level" => arr_get($data, "level", "error"),
            "requestId" => arr_get($data, "requestId", ""),
            "date" => arr_get($data, "date") ?: date("Y-m-d", intval($ms / 1000)),
            "service" => arr_get($data, "service", ""),
            "env" => arr_get($data, "env", Conf::get("env")),
            "ip" => arr_get($data, "ip", ""),
            "call" => arr_get($data, "call", ""),
            "duration" => arr_get($data, "duration", -1),
            "path" => arr_get($data, "path", ""),
            "status" => arr_get($data, "status", -1),
            "appId" => (int)arr_get($data, "appId", -1),
            "keyword1" => arr_get($data, "keyword1", ""),
            "keyword2" => arr_get($data, "keyword2", ""),
            "keyword3" => arr_get($data, "keyword3", ""),
            "msg" => arr_get($data, "msg", ""),
            "context" => arr_get($data, "context", "{}"),
            "file" => arr_get($data, "file", ""),
            "line" => arr_get($data, "line", -1),
        ];

        self::send(json_encode($p, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }


    protected static function send(string $data): void
    {

        if (self::$stream == null) {
            self::$stream = stream_socket_client("udp://log.stat.com:9823", $errno, $error);
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
        $type = pack('n', 61);
        $remain = pack('C', 1);
        $size = pack('N', $length);

        if ($length > 10235) {
            return "";
        }

        return $type . $remain . $size . $data;
    }


}

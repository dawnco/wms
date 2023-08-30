<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-03
 */

namespace Wms\Fw;

class Log
{
    public static function debug(string $msg): void
    {
        if (Conf::get('app.log.level') == 'debug') {
            self::record("debug", $msg);
        }
    }

    public static function info(string $msg): void
    {
        if (in_array(Conf::get('app.log.level'), ['debug', 'info'])) {
            self::record("info", $msg);
        }
    }

    public static function error(string $msg): void
    {
        self::record("error", $msg);
    }

    public static function record(string $name, string $msg): void
    {
        $dateTime = new \DateTime();
        $dateTime->setTimezone(new \DateTimeZone('Asia/Shanghai'));
        $date = $dateTime->format('Y-m-d H:i:s');
        $serviceName = Conf::get('app.app_name');
        $category = $name;
        $tag = "";
        $requestId = "";
        $time = intval(microtime(true) * 1000);

        self::recordFull($date,
            $serviceName,
            $category,
            $tag,
            $requestId,
            $time,
            ["message" => $msg]);
    }

    /**
     * @param string|null $date
     * @param string|null $serviceName
     * @param string|null $category
     * @param string|null $tag
     * @param string|null $requestId
     * @param int|null    $time
     * @param array       $data
     * @return void
     */
    public static function recordFull(
        ?string $date,
        ?string $serviceName,
        ?string $category,
        ?string $tag,
        ?string $requestId,
        ?int $time,
        ?array $data
    ): void {

        $dir = Conf::get('app.log.dir');

        $msg = sprintf("[%s] [%s] [%s] [%s] [%s] [%s] %s\n",
            $date,
            $serviceName,
            $category,
            $tag,
            $requestId,
            $time,
            json_encode($data));

        /**
         * 日志格式 [北京时间] [服务] [一级分类] [二级分类] [requestId] [time] msg
         * 日志例子 [2022-06-13 14:13:29] [service] [info] [] [7e484c28-92a6-c679-4835-a2d3fa418334] [1655100809772] "hello"
         * @param string      $category 分类
         * @param mixed       $message  日志内容
         * @param string      $tag      标签
         * @param int         $time     毫秒
         * @param string|null $requestId
         * @return void
         */

        file_put_contents($dir . "/$serviceName-$category-" . date("Y-m-d") . ".log",
            $msg,
            FILE_APPEND);
    }
}

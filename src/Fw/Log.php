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
        $dir = Conf::get('app.log.dir');
        if (!$dir) {
            $dir = APP_PATH . "/Runtime";
        }
        file_put_contents($dir . "/$name-" . date("Y-m-d") . ".log",
            sprintf("[%s] [%s] %s\n", date("Y-m-d H:i:s"), $name, $msg),
            FILE_APPEND);
    }
}

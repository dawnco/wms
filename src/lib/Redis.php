<?php
/**
 * @author Dawnc
 * @date   2020-05-24
 */

namespace Wms\Lib;


use Wms\Exception\WmsException;
use Wms\Fw\Conf;

class Redis
{

    private static array $instance = [];

    /**
     * @param string $name
     * @return \Redis
     * @throws WmsException
     */
    public static function getInstance(string $name = 'default'): \Redis
    {
        if (!isset(self::$instance[$name])) {
            $conf = Conf::get("app.redis.$name");
            self::$instance[$name] = new \Redis();
            $ok = self::$instance[$name]->connect(
                $conf['hostname'] ?? '127.0.0.1',
                $conf['port'] ?? 6379,
                $conf['timeout'] ?? 0.0);

            if (!$ok) {
                throw new WmsException("Redis connect error");
            }

            $conf['password'] = $conf['password'] ?? null;
            if ($conf['password']) {
                self::$instance[$name]->auth($conf['password']);
            }
            self::$instance[$name]->select($conf['db'] ?? 0);
        }
        return self::$instance[$name];
    }

}

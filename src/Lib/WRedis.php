<?php
/**
 * @author Dawnc
 * @date   2020-05-24
 */

namespace Wms\Lib;


use Redis;
use Wms\Exception\WmsException;
use Wms\Fw\Conf;

class WRedis
{

    private static array $instance = [];

    /**
     * @param string $name
     * @return Redis
     * @throws WmsException
     * @throws \RedisException
     */
    public static function connection(string $name = 'default'): Redis
    {
        if (isset(self::$instance[$name])) {
            return self::$instance[$name];
        }

        $conf = Conf::get("app.redis.{$name}");

        self::$instance[$name] = new Redis();
        $connected = self::$instance[$name]->connect(
            $conf['hostname'] ?? '127.0.0.1',
            $conf['port'] ?? 6379,
            $conf['timeout'] ?? 10);

        if (!$connected) {
            throw new WmsException("Redis connect error");
        }

        $conf['password'] = $conf['password'] ?? null;
        if ($conf['password']) {
            self::$instance[$name]->auth($conf['password']);
        }
        self::$instance[$name]->select($conf['db'] ?? 0);
        return self::$instance[$name];
    }

}

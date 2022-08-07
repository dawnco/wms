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
     * @param array $conf
     * @return Redis
     * @throws WmsException
     */
    public static function connection(array $conf = []): Redis
    {

        if (!$conf) {
            $conf = Conf::get('app.redis.default');
        }
        $name = md5(json_encode($conf));

        if (self::$instance[$name]) {
            return self::$instance[$name];
        }

        self::$instance[$name] = new Redis();
        $connected = self::$instance[$name]->connect(
            $conf['hostname'],
            $conf['port'],
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

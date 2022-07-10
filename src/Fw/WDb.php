<?php

declare(strict_types=1);

/**
 * @author Hi Developer
 * @date   2022-05-21
 */

namespace Wms\Fw;

use Wms\Database\Connection;

/**
 * @method static insert(string $table, array $data = [])
 * @method static replace(string $table, array $data = [])
 * @method static insertGetId(string $table, array $data = [])
 * @method static insertBatch(string $table, array $data = [])
 * @method static delete(string $table, array $where = [])
 * @method static update(string $table, array $data, array $where)
 * @method static getLine(string $query, array $params = [], string $className = 'stdClass')
 * @method static getData(string $query, array $params = [], string $className = 'stdClass')
 * @method static getVar(string $query, array $params = [])
 * @method static execute(string $query, array $params = [])
 */
class WDb
{

    protected static array $connections = [];

    /**
     * @param string $confName
     * @return Connection
     */
    public static function connection(string $confName = 'default'): Connection
    {
        if (!isset(self::$connections[$confName])) {
            $conf = Conf::get("app.db.$confName");
            self::$connections[$confName] = new Connection(
                [
                    'hostname' => $conf['hostname'] ?? '127.0.0.1',
                    'port' => $conf['port'] ?? 3306,
                    'database' => $conf['database'],
                    'username' => $conf['username'] ?? null,
                    'password' => $conf['password'] ?? null,
                    'timezone' => $conf['timezone'] ?? null,
                ]
            );
        }
        return self::$connections[$confName];
    }

    /**
     * @param string $confName
     * @return void
     */
    public static function release(string $confName = 'default'): void
    {
        unset(self::$connections[$confName]);
    }

    public static function __callStatic($name, $arg)
    {
        $connection = self::connection();
        return $connection->{$name}(...$arg);
    }
}

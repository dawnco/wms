<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2023-02-24
 */

namespace Wms\Lib\Lock;

use Wms\Lib\WRedis;

class WmsLocker
{
    private \Redis $redis;


    private string $lockKey;
    private int $lockExpire;
    private string $redisPoolName;

    /**
     * @param string $lockKey       锁 KEY
     * @param int    $lockExpire    锁的过期时间 (秒)
     * @param string $redisPoolName redis 连接池
     */
    public function __construct(
        string $lockKey = 'slk:k',
        int $lockExpire = 10,
        string $redisPoolName = 'default'
    ) {

        $this->lockKey = $lockKey;
        $this->lockExpire = $lockExpire;
        $this->redisPoolName = $redisPoolName;

        $this->redis = WRedis::connection($this->redisPoolName);
    }

    /** 获取锁 获取成功返回true 否则返回false
     * @return bool
     */
    public function lock(): bool
    {
        $ok = $this->redis->set($this->lockKey, 1, ['nx', 'ex' => $this->lockExpire]);
        return (bool)$ok;
    }

    /**
     * 解锁
     * @return bool
     * @throws \RedisException
     */
    public function unlock(): bool
    {
        return (bool)$this->redis->del($this->lockKey);
    }
}

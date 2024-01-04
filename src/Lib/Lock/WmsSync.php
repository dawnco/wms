<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2023-02-24
 */

namespace Wms\Lib\Lock;

class WmsSync
{

    private WmsLocker $locker;
    /**
     * @var int 未获取锁最大等待时间(秒)
     */
    private int $lockExpire = 10;

    /**
     * @param string $lockKey    锁key
     * @param int    $lockExpire 锁定时间(秒)
     */
    public function __construct(string $lockKey = 'slk:k', int $lockExpire = 10, string $redisPoolName = 'default')
    {
        $this->lockExpire = $lockExpire;
        $this->locker = new WmsLocker($lockKey, $lockExpire, $redisPoolName);
    }

    /**
     * 获得锁执行 $closure 得不到等待直到超时
     * @param \Closure $closure
     * @return void
     * @throws WmsGetLockException
     */
    public function sync(\Closure $closure): void
    {

        if ($this->locker->lock()) {
            // 获得锁
            try {
                $closure();
            } finally {
                $this->locker->unlock();
            }
        } else {
            // 未获得锁 循环等待
            $start = intval(microtime(true) * 1000);
            $maxWait = $this->lockExpire * 1000;
            do {
                usleep(10000);
                if ($this->locker->lock()) {
                    try {
                        $closure();
                        return;
                    } finally {
                        $this->locker->unlock();
                    }
                }
            } while ((intval(microtime(true) * 1000) - $start) <= $maxWait);

            throw new WmsGetLockException("未获取锁");
        }

    }

}

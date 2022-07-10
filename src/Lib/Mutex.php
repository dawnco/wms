<?php
/**
 *  redis 并发锁
 * @author Dawnc
 * @date   2020-07-27
 */

namespace Wms\Lib;


use Throwable;
use Wms\Exception\WmsException;

class Mutex
{
    private ?\Redis $redis = null;
    private string $prefix = "mutex:";
    private string $name = '';

    /**
     * @var int 锁过期时间 毫秒
     */
    private int $lockExpire = 8000;


    /**
     * @param string $name 锁的名称
     * @param null   $redis
     * @throws WmsException
     */
    public function __construct(string $name, $redis = null)
    {
        $this->name = $name;
        $this->redis = $redis ?: Redis::getInstance();
    }

    /**
     * 获得锁的 执行 success 没有执行 fail
     * @param callable      $callback 获取锁执行的回调
     * @param callable|null $fail     获取锁失败执行的回调
     * @return mixed
     * @throws Throwable
     */
    public function mutex(callable $callback, ?callable $fail = null)
    {
        $lock = $this->lock();
        if ($lock) {
            try {
                $result = $callback();
                $this->unlock();
                return $result;
            } catch (Throwable $e) {
                $this->unlock();
                throw $e;
            }
        } else {
            if ($fail) {
                return $fail();
            }
        }

    }


    /**
     * 等待一个操作,未获得锁则继续等待
     * @param callable $callback
     * @return mixed
     * @throws Throwable
     */
    public function synchronized(callable $callback): mixed
    {
        $lock = $this->lock();

        while (!$lock) {
            // 1微秒（micro second）是百万分之一秒
            usleep(50000);
            $lock = $this->lock();
        }

        try {
            $result = $callback();
            $this->unlock();
            return $result;
        } catch (Throwable $e) {
            $this->unlock();
            throw $e;
        }

    }

    protected function lock(): bool
    {
        return (bool)$this->redis->set($this->prefix . $this->name, 1, ['nx', 'px' => $this->lockExpire]);
    }

    protected function unlock(): bool
    {
        return (bool)$this->redis->del($this->prefix . $this->name);
    }

}

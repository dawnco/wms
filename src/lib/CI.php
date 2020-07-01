<?php
/**
 * @author Dawnc
 * @date   2020-06-29
 */

namespace lib;

use Swoole\Event;
use Swoole\Http\Server;
use Swoole\Timer;

/**
 * 持续集成
 * Class CI
 * @package lib
 */
class CI
{
    public function start()
    {
        date_default_timezone_set('PRC');
        swoole_set_process_name("gitAutoPullPhp");
        $http = new Server("0.0.0.0", 8008);
        $http->set(array(
            'reactor_num'   => 1,
            'worker_num'    => 1,
            'backlog'       => 128,
            'max_request'   => 50,
            'dispatch_mode' => 1,
            'daemonize'     => 1,
            'log_level'     => SWOOLE_LOG_ERROR,
            'log_file'      => "/tmp/swoole.log",
        ));
        $http->set(['enable_coroutine' => true]);
        $http->on('request', function ($request, $response) {
            Event::defer(function () {
                $this->exec();
            });
            $response->end("ok");
        });

        $http->start();
    }


    public function timer()
    {
        Timer::after(1000, function () {
            $this->exec();
            $this->timer();
        });
    }

    public function exec()
    {
        $this->git("/www/api-v3/.git");
        $this->git("/www/loan-app-test/.git");
        $this->git("/www/loan-app-admin/.git");

    }

    protected function git($dir)
    {
        $output = exec("git --git-dir=$dir pull");
        echo date('Y-m-d H:i:s '),
        $dir, ' ', $output, "\n";

    }

}

$cls = new CI();
$cls->start();

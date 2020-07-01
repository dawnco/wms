<?php
/**
 * @author Dawnc
 * @date   2020-04-22
 */

namespace wms\lib;

use app\dict\ErrorCode;
use wms\fw\Exception;

class Sms
{
    /** 短信最小间隔时间 */
    const  SEND_NEXT_TIME = 1;

    /** 一天最多发多少条 */
    const  SEND_MAX_DAY   = 10000;

    public static function send($phone)
    {

        if (!$phone) {
            throw new Exception("手机号码不能为空", ErrorCode::SMS_SEND_ERROR);
        }

        $redis = Redis::getInstance();
        $last  = $redis->get('sms:last:' . $phone);
        $now   = time();
        if ($last && $now - $last < self::SEND_NEXT_TIME) {
            throw new Exception(sprintf("发送过于频繁，%d秒后在重试！", self::SEND_NEXT_TIME), ErrorCode::SMS_SEND_ERROR);
        }

        // 同一个手机号每天发送限制发送10条短信
        $times = $redis->get('sms:stat:' . date("d", $now) . ":" . $phone);
        if ($times > self::SEND_MAX_DAY) {
            throw new Exception("该手机号码今日短信数量已达上限！", ErrorCode::SMS_SEND_ERROR);
        }

        //生成验证码
        $code = "";
        for ($i = 0; $i < 6; $i++) {
            $code .= rand(0, 9);
        }
        $redis->setex('sms:last:' . $phone, self::SEND_NEXT_TIME + 60, $now);
        $redis->incr('sms:stat:' . date("d", $now) . ":" . $phone);
        $redis->expire('sms:stat:' . date("d", $now) . ":" . $phone, 3600 * 24 + 3600);
        $redis->setex('sms:code:' . $phone, 600, $code);
        self::sendSms($phone, $code);

        return $code;
    }

    public static function verify($phone, $code)
    {

        $redis = Redis::getInstance();
        $saved = $redis->get('sms:code:' . $phone);

        if (!$phone || !$saved || !$code) {
            return false;
        }

        return $saved == $code;
    }


    /**
     * 发短信
     * @param $mobile
     * @param $code
     */
    private static function sendSms($mobile, $code)
    {

    }


}

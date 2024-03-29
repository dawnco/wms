<?php

declare(strict_types=1);

/**
 * @author Dawnc
 * @date   2022-07-03
 */

namespace Wms\Constant;

class ErrorCode
{
    /**
     * 系统错误
     */
    const  SYSTEM_ERROR = 10;

    /**
     * 数据库错误
     */
    const  DATABASE_ERROR = 20;

    /**
     * 网络错误
     */
    const  NETWORK_ERROR = 30;

    /**
     * 页面不存在
     */
    const  PAGE_NOT_FOUND = 40;

    /**
     * 未登录
     */
    const NOT_LOGIN = 1002;

    /**
     * 没有权限
     */
    const NO_PERMISSION = 1003;
}

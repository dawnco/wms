<?php
/**
 * @author Dawnc
 * @date   2020-05-23
 */

namespace app\dict;


class ErrorCode
{
    /**
     * 参数错误
     */
    const PARAM_ERROR = 1;

    /**
     * 产品不存在
     */
    const PRODUCT_NOT_FOUND = 2;
    /**
     * 服务不可用
     */
    const SERVICE_UNDEFINED = 3;
    /**
     * 页面不存在
     */
    const PAGE_NOT_FOUND = 4;

    /**
     * 用户未登录
     */
    const USER_NOT_LOGIN = 5;

}

<?php
/**
 * @author Dawnc
 * @date   2020-06-03
 */

namespace wms\lib;


class Base64Image
{
    /**
     * 转换 base64格式图片 到二进制
     * @param $base64Str  base64格式的图片字符串数据
     */
    function bin($base64Str)
    {
        $arr = explode(',', $base64Str);
        $bin = base64_decode(arr_get($arr, 1));
        return $bin;
    }

    /**
     *图片文件转base64格式的图片字符串
     * @param $imageFile 图片路径
     *                   return 图片字符串
     */
    function base64($imageFile)
    {
        $info = getimagesize($imageFile);
        $src  = "data:{$info['mime']};base64," . base64_encode(file_get_contents($imageFile));
        return $src;
    }

    /**
     * 获取二进制图片文件的扩展名 如果不是图片数据则返回false
     * @param $bin 二进制图片数据流
     *             return 图片扩展名
     */
    public function ext($bin)
    {

        $bits = array(
            'jpg' => "\xFF\xD8\xFF",
            'gif' => "GIF",
            'png' => "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a",
            'bmp' => 'BM',
        );

        foreach ($bits as $type => $bit) {
            if (substr($bin, 0, strlen($bit)) === $bit) {
                return $type;
            }
        }
        return false;

    }

}

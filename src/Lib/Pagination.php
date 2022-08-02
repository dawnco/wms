<?php

namespace Wms\Lib;

/**
 * 分页类
 */
class Pagination
{

    private int $total; //总记录数
    private int $totalPage; //总页数
    private int $size; //每页记录数
    private int $currentPage; //当前页码
    private int $page = 0; //指定的当前页
    private string $pageTag = "{page}"; //页面变量模板
    private string $pageVar = "page"; //page 参数变量
    private int $showNum = 10; //显示多少个页码
    private string $pageUrl = ''; // 分页url 模板
    private string $firstPageUrl = ""; //第一页地址
    private int $startNum;
    private int $endNum;

    /**
     * @param array $option [
     *                      "total" => 总记录数,
     *                      "page" => 当前页,
     *                      "size"=>每页数目
     *                      "pageUrl"=> 分页url模板
     *                      "firstPageUrl" => 第一页url
     *                      ]
     */
    public function __construct(array $option)
    {

        foreach ($option as $key => $value) {
            $this->$key = $value;
        }

        $this->totalPage = ceil(($this->total ? $this->total : 1) / $this->size);

        //最大页数限制
        if (isset($option['maxPage'])) {
            $this->totalPage = $this->totalPage <= $option['maxPage'] ? $this->totalPage : $option['maxPage'];
        }

        //指定的页码
        $this->currentPage = $this->page;

        if ($this->currentPage > $this->totalPage) {
            $this->currentPage = $this->totalPage;
        }

        $this->calcPageNum();
    }


    /**
     * 计算偏移量
     */
    private function calcPageNum()
    {

        //显示几个
        $length = ceil($this->showNum / 2);

        if ($this->currentPage <= $length) {
            //前4页
            $this->startNum = 1; //起始页
            $this->endNum = $this->showNum < $this->totalPage ? $this->showNum : $this->totalPage;
        } elseif ($this->currentPage >= $this->totalPage - $length) {
            //最有4页
            $this->endNum = $this->totalPage;
            $start = $this->endNum - $this->showNum + 1;
            $this->startNum = max($start, 1); //起始页
        } else {
            $start = $this->currentPage - $length + 1;
            $end = $start + $this->showNum - 1;

            if ($start == 0) {
                $start = 1;
                $end = $this->showNum;
            }

            $this->startNum = $start; //起始页
            $this->endNum = $end < $this->totalPage ? $end : $this->totalPage;
        }

    }

    /**
     * 生成url
     * @param int $number
     * @return string
     */
    private function url(int $number): string
    {

        if ($number == 1 && $this->firstPageUrl) {
            //自定义首页地址
            return $this->firstPageUrl;
        } else {
            return str_replace($this->pageTag, $number, $this->pageUrl);
        }
    }

    /**
     * 产生分页html;
     * @return string
     */
    public function html()
    {
        $str = "<span>" . $this->total . " 条记录 </span>";
        //只有一页
        if ($this->totalPage == 1) {
            return $str;
        }

        if ($this->currentPage > 1) {
            $str .= "<a href=\"" . $this->url(1) . "\">首页</a>";
        }

        for ($i = $this->startNum; $i <= $this->endNum; $i++) {
            if ($i == $this->currentPage) {
                $str .= "<span class=\"active\">" . $i . "</span>";
            } else {
                $str .= "<a href=\"" . $this->url($i) . "\">" . $i . "</a>";
            }
        }

        if ($this->currentPage < $this->totalPage && $this->totalPage > 1) {
            $str .= "<a href=\"" . $this->url($this->currentPage + 1) . "\">下一页</a>";
        }

        if ($this->currentPage == $this->totalPage) {
            $str .= "<span class=\"active last\">末页</span>";
        } else {
            $str .= "<a href=\"" . $this->url($this->totalPage) . "\">末页</a>";
        }

        return $str;
    }

    /**
     * mysql LIMIT 部分数据
     * @return string
     */
    public function limit(): string
    {
        return " LIMIT " . (($this->currentPage - 1) * $this->size) . "," . $this->size . " ";
    }

    /**
     * 总页数
     * @return float
     * @author  Dawnc
     */
    public function getTotalPage()
    {
        return $this->totalPage;
    }

    /**
     * 当前页码
     * @return int
     */
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * 最后一页
     * @return bool
     */
    public function ended(): bool
    {
        return $this->currentPage == $this->totalPage;
    }

    /**
     * 每页大小
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->size;
    }

}

<?php

namespace Collection\Controller;


use QL\QueryList;

class IndexController extends CollectionController
{
    public function index()
    {
        $url   = "https://www.biquge.com.cn/book/32883/";
        $ql    = QueryList::get($url);
        $title = $ql->find('title')->text(); // 获取网站标题
        $texts = $ql->find('dd>a')->texts(); //获取搜索结果标题列表
        $hrefs = $ql->find('dd>a')->attrs('href'); //获取搜索结果链接列表
        dump($title,$texts,$hrefs);
    }
}
<?php

namespace Admin\Controller;


use QL\QueryList;

class IndexController extends CollectionController
{
    public function index()
    {
        $url   = "https://www.biquge.com.cn/book/32883/";
        $ql    = QueryList::get($url);
        $title = $ql->find('title')->text(); // 获取网站标题
        dump($title);
    }
}
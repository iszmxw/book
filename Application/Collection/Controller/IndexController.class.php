<?php

namespace Collection\Controller;


use QL\QueryList;

class IndexController extends CollectionController
{
    /**
     *
     * @author：iszmxw <mail@54zm.com>
     * @time：2019/12/16 20:20
     */
    public function index()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        while (1) {
            $res = $redis->lpop('listname');
            if ($res) {
                echo $res . "\r\n";
            }
            sleep(1);
        }
    }


    public function collections()
    {
        $url   = "https://www.biquge.com.cn/book/32883/";
        $ql    = QueryList::get($url);
        $title = $ql->find('#info>h1')->text(); // 获取小说标题
        $texts = $ql->find('dd>a')->texts(); //获取搜索结果标题列表
        $hrefs = $ql->find('dd>a')->attrs('href'); //获取搜索结果链接列表
        dump($title, $texts, $hrefs);
    }
}
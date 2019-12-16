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
        $redis->connect('118.89.61.124', 4399);
        $redis->auth('blog_54zm_com');              //密码验证
        $redis->select(2);                          //选择数据库2
        $url    = "https://www.biquge.com.cn/book/32883/";
        $data   = self::collections($url);
        $result = [];
        foreach ($data['texts'] as $key => $val) {
            $result[$key]['title'] = $data['texts'][$key];
            $result[$key]['url']   = "https://www.biquge.com.cn" . $data['hrefs'][$key];
        }
        $res = $redis->lPush('iszmxw', time());
        dump($result);
//        while (1) {
//            $res = $redis->lPush(time());
//            if ($res) {
//                echo $res . "\r\n";
//            }
//            sleep(1);
//        }
    }


    public static function collections($url)
    {
        $ql    = QueryList::get($url);
        $title = $ql->find('#info>h1')->text(); // 获取小说标题
        $texts = $ql->find('dd>a')->texts(); //获取搜索结果标题列表
        $hrefs = $ql->find('dd>a')->attrs('href'); //获取搜索结果链接列表
        return [
            'title' => $title,
            'texts' => $texts,
            'hrefs' => $hrefs,
        ];
    }
}
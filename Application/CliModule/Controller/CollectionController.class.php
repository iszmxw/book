<?php

namespace CliModule\Controller;

use GuzzleHttp\Client;
use QL\QueryList;
use Think\Controller;

class CollectionController extends Controller
{
    protected $redis;

    public function _initialize()
    {
        //  你可以在此覆盖父类方法
        $this->redis = new \Redis();
        $this->redis->connect('118.89.61.124', 4399);
        $this->redis->auth('blog_54zm_com');              //密码验证
        $this->redis->select(1);                          //选择数据库1
    }

    /**
     *
     * @return mixed|void
     * @author: iszmxw <mail@54zm.com>
     * @Date：2020/1/13 16:43
     */
    public function get_data()
    {
        // 自动执行脚本，每相隔2秒访问一次广告接口，并且打印出日志
        while (1) {
            $url  = "https://www.biquge.com.cn/book/32883/";
            $data = self::collections($url);
            foreach ($data['hrefs'] as $key => $val) {
                $json = json_encode(['title' => $data['title'], 'url' => "https://www.biquge.com.cn" . $data['hrefs'][$key]], true);
                $res  = $this->redis->lPush('book', $json);
                dump($res);
            }
            sleep(2);
        }
    }


    /**
     * 找出标题和地址
     * @param $url
     * @return array
     * @author: iszmxw <mail@54zm.com>
     * @Date：2020/1/13 17:00
     */
    public static function collections($url)
    {
        $ql    = QueryList::get($url);
        $title = $ql->find('#info>h1')->text(); // 获取小说标题
        $hrefs = $ql->find('dd>a')->attrs('href'); //获取搜索结果链接列表
        return [
            'title' => $title,
            'hrefs' => $hrefs,
        ];
    }
}
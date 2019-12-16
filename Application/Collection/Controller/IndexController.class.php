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
        $url  = "https://www.biquge.com.cn/book/32883/";
        $data = self::collections($url);
        foreach ($data['hrefs'] as $key => $val) {
            $json = json_encode(['title' => $data['title'], 'url' => "https://www.biquge.com.cn" . $data['hrefs'][$key]], true);
            $res  = $redis->lPush('iszmxw', $json);
            dump($res);
        }
    }


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


    public function zip()
    {
        $redis = new \Redis();
        $redis->connect('118.89.61.124', 4399);
        $redis->auth('blog_54zm_com');              //密码验证
        $redis->select(2);                          //选择数据库2
        $res = $redis->lpop('iszmxw');
        if ($res) {
            $data = json_decode($res, true);
            $this->handlezip($data['title'], $data['url']);
        }

        die();

//        while (1) {
//
//            sleep(1);
//        }

    }


    /**
     * 压缩zip
     * @param $title
     * @param $url
     * @author：iszmxw <mail@54zm.com>
     * @time：2019/12/17 0:39
     */
    public function handlezip($title, $url)
    {
        $ql       = QueryList::get($url);
        $chapter  = $ql->find('.bookname>h1')->text(); // 获取小说章节标题
        $filename = "/iszmxw/$chapter.txt";
        $content  = $ql->find('#content')->html(); // 获取小说内容
        $content  = str_replace('<br><br>', "\r\n", $content); // 处理小说内容
        $re       = file_put_contents($filename, $content);
        if ($re) {
            $zipname = "$title.zip";
            $zip     = new \ZipArchive();
            if ($zip->open($zipname) === TRUE) {
                $zip->addFile($filename);
                $zip->close();
                dump("第二次添加文件到压缩包");
            } else {
                // 文件集合
                $fileList = array(
                    realpath($filename)
                );
                $zip->open($zipname, \ZipArchive::CREATE);   //打开压缩包
                foreach ($fileList as $file) {
                    $zip->addFile($file, basename($file));   //向压缩包中添加文件
                }
                $zip->close();  //关闭压缩包
                dump("首次创建压缩包");
            }
        }
    }

}
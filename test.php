<?php
$redis = new \Redis();
$redis->connect('118.89.61.124', 4399);
$redis->auth('blog_54zm_com');              //密码验证
$redis->select(2);                          //选择数据库2
while (1) {
    $res = $redis->lpop('iszmxw');
    if ($res) {
        echo $res . "\r\n";
    }
    sleep(1);
}
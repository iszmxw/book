<?php

namespace Home\Controller;

use Think\Controller;

class MyPayController extends Controller
{
    public function _initialize()
    {
        // 加载配置
        $config = M('config')->select();
        if (!is_array($config)) {
            die('请先在后台设置好各参数');
        }
        foreach ($config as $v) {
            $key              = '_' . $v['name'];
            $this->$key       = unserialize($v['value']);
            $_CFG[$v['name']] = $this->$key;
        }
        $GLOBALS['_CFG'] = $_CFG;
    }


    // 充值支付通知异步页面
    public function index()
    {
        $pay_result = file_get_contents('php://input');
        if ($pay_result) {
            // 打印日志
            IszmxwLog('iszmxw.txt', $pay_result);
            return 'success';
        }
        return 'error';
    }
}
<?php

// 对字符串进行加盐散列加密
function xmd5($str)
{
    return md5(md5($str) . C('SAFE_SALT'));
}

// 获得当前的url
function get_current_url()
{
    $url = "http://" . $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

// 补全url
function complete_url($url)
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    if (substr($url, 0, 1) == '.') {
        return $protocol . $_SERVER['SERVER_NAME'] . __ROOT__ . substr($url, 1);
    } elseif (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {
        return $protocol . $_SERVER['SERVER_NAME'] . $url;
    } else {
        return $url;
    }

}


//二维数组排序
function arraySort($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}


// 根据订单状态返回状态信息
function get_order_status($status)
{
    $status_str = '';
    switch ($status) {
        case -1:
            $status_str = '已关闭';
            break;
        case 1:
            $status_str = '待支付';
            break;
        case 2:
            $status_str = '已支付待发货';
            break;
        case 3:
            $status_str = '待确认';
            break;
        case 4:
            $status_str = '已完成';
            break;
        default :
            $status_str = '未知状态';
    }
    return $status_str;
}


//根据金额获取对应等级
function get_lv_money($money)
{
    if (!$money) {
        return;
    }
    $config = $GLOBALS['_CFG']['lv'];
    for ($i = count($config); $i > 0; $i--) {
        if ($money >= $config[$i]['money']) {
            return $i;
            break;
        }
    }
    return false;
}

//微信支付方法
function wxPay($order, $table = '', $type = '')
{
    if (!is_array($order)) {
        $order = M($table)->find(intval($order));
    }
    $user  = M('user')->find(intval($order['user_id']));
    $jsapi = new \Common\Util\wxjspay;

    $param                 = $GLOBALS['_CFG']['mp'];
    $param['key']          = $GLOBALS['_CFG']['mp']['key'];
    $param['openid']       = $user['openid'];
    $param['body']         = $$GLOBALS['_CFG']['site']['name'] . '在线支付';
    $param['out_trade_no'] = $order['sn'];
    $param['total_fee']    = $order['money'] * 100;
    $param['attach']       = json_encode(array(
        'order_id' => $order['id'],
        'table'    => $table,
        'type'     => $type,
    ));
    $param['notify_url']   = "http://" . $_SERVER['HTTP_HOST'] . __ROOT__ . '/notify.php';

    $jsapi->set_param($param);
    $uo           = $jsapi->unifiedOrder();
    $jsapi_params = $jsapi->get_jsApi_parameters();
    return $jsapi_params;
}


// 根据用户信息取得推广二维码路径信息
function get_qrcode_path($user)
{
    if (!is_array($user)) {
        $user = M('user')->find($user);
    }

    $path = './Public/qrcode/' . date('ym/d/', $user['sub_time']);
    return array(
        'path'      => $path,
        'new'       => $path . $user['id'] . '_dragondean.jpg',
        'head'      => $path . $user['id'] . '_head.jpg',
        'qrcode'    => $path . $user['id'] . '_qrcode.jpg',
        'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path, 1)
    );
}


// 根据用户信息取得推广二维码路径信息
function getAgentQrcode($imei)
{
    if (!$imei) {
        return false;
    }
    $path = './Public/imei/';
    return array(
        'path'      => $path,
        'qrcode'    => $path . $imei . '_qrcode.jpg',
        'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path, 1),
    );
}


// 根据用户信息取得推广二维码路径信息
function getChapQrcode($id)
{
    if (!$id) {
        return false;
    }
    $path = './Public/chapter/';
    return array(
        'path'      => $path,
        'qrcode'    => $path . $id . '_qrcode.jpg',
        'full_path' => $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . substr($path, 1),
    );
}

// 根据订漫画分类返回分类名
function get_mh_cate_name($status)
{
    $status_str = 'error';
    switch ($status) {
        case 1:
            $status_str = '总裁';
            break;
        case 2:
            $status_str = '穿越';
            break;
        case 3:
            $status_str = '校园';
            break;
        case 4:
            $status_str = '恐怖';
            break;
        case 5:
            $status_str = '古风';
            break;
        case 6:
            $status_str = '恋爱';
            break;
        case 7:
            $status_str = '奇幻';
            break;
        case 8:
            $status_str = '热血';
            break;
        case 9:
            $status_str = '悬疑';
            break;
        case 10:
            $status_str = '耽美';
            break;
        case 11:
            $status_str = '都市';
            break;
        case 12:
            $status_str = '爆笑';
            break;
        case 13:
            $status_str = '真人';
            break;
    }
    return $status_str;
}

/** 添加财务日志
 *    type => money:余额记录,points:积分记录
 */
function flog($user_id, $type, $money, $action)
{
    M('finance_log')->add(array(
        'user_id'     => $user_id,
        'type'        => $type,
        'money'       => $money,
        'action'      => $action,
        'create_time' => NOW_TIME
    ));
}

/**
 *订单生成分成记录
 */
function separate($order)
{
    if (!is_array($order)) {
        $order = M('order')->find(intval($order));
    }
    if (!$order) {
        return false;
    }
    // 如果订单已分成则退出
    if ($order['separate'] > 0) {
        return false;
    }

    $user = M('user');

    // 查询用户信息
    $user_info = $user->find($order['user_id']);
    if (!$user_info) {
        return false;
    }

    $site = $GLOBALS['_CFG']['site'];

    // 如果是商品设置分成总额分成则计算可分成总额
    if ($site['dist'] == 2) {
        $total = $order['separate_money'];
    } // 否则分成金额就是订单总额
    else {
        $total = $order['money'];
    }
    // 循环分红
    for ($i = 1; $i <= 3; $i++) {
        // 检查是否有这一级别的上级
        if (empty($user_info['parent' . $i]) || $user_info['parent' . $i] < 1) {
            break;
        }

        // 查询上级资料
        $parent_info = $user->find($user_info['parent' . $i]);

        if (!$parent_info) {
            break; // 这级别代理都木有就没有在上一级了，直接跳出循环
        }

        //若上级合伙人或代理级别都没有则不分成
        if ($parent_info['level'] < 1 && $parent_info['lv'] < 1) {
            continue;
        }

        // 若等级为合伙人，则分合伙人等级分成
        if ($parent_info['level'] > 0 && $parent_info['lv'] > 0) {
            $dist = $GLOBALS['_CFG']['level'][$parent_info['level']]['separate' . $i];
            $type = 'level';
        } elseif ($parent_info['level'] > 0 && $parent_info['lv'] < 1) {
            $dist = $GLOBALS['_CFG']['level'][$parent_info['level']]['separate' . $i];
            $type = 'level';
        } elseif ($parent_info['level'] < 1 && $parent_info['lv'] > 0) {
            $dist = $GLOBALS['_CFG']['lv'][$parent_info['lv']]['separate' . $i];
            $type = 'lv';
        }
        //若分成比例没设置，则退出
        if (!$dist) {
            continue;
        }
        // 进行分红
        $separate_money = $total * $dist / 100; // 分红金额
        M('separate_log')->add(array(
            'user_id'     => $user_info["parent{$i}"],
            'order_id'    => $order['id'],
            'self_id'     => $order['user_id'],
            'level'       => $i,
            'money'       => $separate_money,
            'status'      => 1,
            'type'        => $type,
            'create_time' => NOW_TIME
        ));

        M('order')->where('id=' . $order['id'])->setInc('separate', 1);
    }
}


//更新用户销售业绩和购买金额和产品交易数(以用户真实购买的金额为标准)
function upUser($order)
{
    if (!is_array($order)) {
        $order = M('order')->find(intval($order));
    }
    $separate = M('separate_log')->where(array('order_id' => $order['id']))->select();
    foreach ($separate as $k => $v) {
        //更改用户销售额度
        M('user')->where(array('id' => $v['user_id']))->setInc('sales', $order['money']);
    }

    //更改用户总购买额度
    M('user')->where(array('id' => $order['user_id']))->setInc('btotal', $order['money']);

    //更改产品交易数量
    M('goods')->where(array('id' => array('in', $order['goods_id'])))->setInc('sold', 1);
    //发放佣金
    doSeparate($order);
    //更改用户等级
    $user = M('user')->where(array('id' => $v['user_id']))->find();
    upLv($user);

    return false;
}


/**
 * 获取惟一代理盐值
 * @return string
 */
//生成唯一用户uid
function Salt()
{
    $autoID        = mt_rand(1, 550000);
    $autoCharacter = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E");
    $len           = 7 - ((int)log10($autoID) + 1);
    $i             = 1;
    $numberID      = mt_rand(1, 2) . mt_rand(1, 4);
    for ($i; $i <= $len - 1; $i++) {
        $numberID .= $autoCharacter[mt_rand(1, 13)];
    }
    return base_convert($numberID . "E" . $autoID, 16, 10); //--->这里因为autoid永远不可能为E所以使用E来分割保证不会重复
}


//根据购买额度更改代理等级
function upLv($user)
{
    if (!is_array($user)) {
        $user = M('user')->find(intval($user));
    }
    $lv = get_lv_money($user['btotal']);
    if ($user['lv'] < $lv) {
        M('user')->where(array('id' => $user['id']))->save(array('lv' => $lv));
    }
}

//发放分拥到帐户(总分拥)
function doSeparate($order)
{
    if (!is_array($order)) {
        $order = M('order')->find(intval($order));
    }
    $separate = M('separate_log')->where(array('order_id' => $order['id']))->select();
    foreach ($separate as $v) {
        M('user')->where(array('id' => $v['user_id']))->setInc('money', $v['money']);
        M('user')->where(array('id' => $v['user_id']))->setInc('expense', $v['money']);
        flog($v['user_id'], 'money', $v['money'], 3);
    }
}

function create_order_sn($user_id)
{
    $sn = date("Ymd") . mt_rand(100, 999) . mt_rand(1000, 9999) . $user_id;
    return $sn;
}

//判断是否微信打开
function is_weixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;

}

//发送短信
function sms($mobile, $con)
{
    $site      = $GLOBALS['_CFG']['site'];
    $content   = '【' . $site['smssign'] . '】' . $con;
    $url       = "http://api.smsbao.com/sms?u=" . $site['smsuser'] . "&p=" . md5($site['smspsw']) . "&m=" . $mobile . "&c=" . urlencode($content);
    $rt        = file_get_contents($url);
    $statusStr = array(
        "0"  => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    if ($rt == "0") {
        return 1;
    } else {
        return $statusStr[$rt];
    }
}

/**
 * 对称加密算法之加密
 */
function encode($string = '', $skey = 'Lswig')
{
    $strArr   = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

/**
 * 对称加密算法之解密
 */
function decode($string = '', $skey = 'Lswig')
{
    $strArr   = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}


function http($url, $data, $method = "POST")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    $errorno = curl_errno($ch);
    if (!$errorno) return $tmpInfo;
    else {
        $this->errmsg = $errorno;
        return false;
    }
}

//返回错误
function jsonError($message = '', $url = null)
{
    $return['msg']  = $message;
    $return['data'] = '';
    $return['code'] = -1;
    $return['url']  = $url;
    return json_encode($return);
}

/**
 * 打印日志到txt文件
 * @param $file_name $文件名称
 * @param $content $要存储的内容
 * @author: iszmxw <mail@54zm.com>
 * @Date：2019/12/6 13:51
 */
function IszmxwLog($file_name, $content)
{
    $init_txt = file_get_contents($file_name, true);
    if (false === $init_txt) {
        $content_hr = chr(0xEF) . chr(0xBB) . chr(0xBF) . '时间：' . date('Y-m-d H:i:s') . "======>\r\n";// 时间换行
    } else {
        $content_hr = "\r\n" . '时间：' . date('Y-m-d H:i:s') . "======>\r\n";// 时间换行
    }
    file_put_contents($file_name, $init_txt . $content_hr . $content);
}


if (!function_exists('view')) {
    /**
     *
     * @param $obj
     * @param array $data
     * @author: iszmxw <mail@54zm.com>
     * @Date：2019/12/14 15:20
     */
    function view($obj, $data = [])
    {
        foreach ($data as $key => $val) {
            $obj->assign($key, $val);
        }
    }
}

?>
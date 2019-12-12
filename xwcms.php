<?php
/**
 *    xwcms小说管理系统，由开源框架二次开发
 *
 *    http://blog.54zm.com
 *
 *    手机 : 18576409426
 *
 *    微信:qn54zm
 *
 * ================================================================================
 *
 * 使用协议：
 * 本程序是基于开源框架开发，程序免费开源，版权归xwcms所有。
 * 未经同意不得转售或者修改后再次销售，使用本程序则视为接受本协议。
 *
 * ================================================================================
 *
 * => 安全提示：
 * => 为了管理员登陆方便，管理员登陆界面未设置图形验证码。
 * => 为了安全起见，建议您安装并测试好之后将此文件名改为一个别人猜不到的名字
 * => 比如xwcmsE15.php（不要使用特殊符号，可能导致访问不了）
 */
$_GET['m'] = 'Admin';
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('PHP 版本必须大于等于5.3.0 !');

define('DIR_SECURE_CONTENT', 'powered by http://blog.54zm.com');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);

define('APP_PATH', './Application/');

// 使用composer自动加载器
require './vendor/autoload.php';
require './ThinkPHP/ThinkPHP.php';
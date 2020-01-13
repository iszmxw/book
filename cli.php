<?php
/**
 * 命令行专用模块入口
 * 明航模块
 */
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('PHP 版本必须大于等于5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为 false

define('APP_DEBUG', true);

// 定义命令行模式执行
define('APP_MODE', 'cli');

// 定义模块名
define('BIND_MODULE', 'CliModule');

define('DIR_SECURE_CONTENT', 'powered by http://blog.54zm.com');

define('APP_PATH', './Application/');


// 使用composer自动加载器
require './vendor/autoload.php';
require './ThinkPHP/ThinkPHP.php';
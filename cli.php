<?php
/**
 * 命令行专用模块入口
 */
define('__ROOT__', '/');
define('_PHP_FILE_', $_SERVER['SCRIPT_NAME']);
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('PHP 版本必须大于等于5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为 false
define('APP_DEBUG', false);

// 定义命令行模式执行
define('APP_MODE', 'cli');

// 定义模块名
define('BIND_MODULE', 'CliModule');

// 定义应用目录（linux 下需要写绝对目录）
define('APP_PATH', dirname(__FILE__) . '/Application/');

// 使用composer自动加载器
require dirname(__FILE__) . '/vendor/autoload.php';
// 引入 ThinkPHP 入口文件
require dirname(__FILE__) . '/ThinkPHP/ThinkPHP.php';
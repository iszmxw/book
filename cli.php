<?php
/**
 * 命令行专用模块入口
 * 明航模块
 */
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为 false

define('APP_DEBUG', true);

// 定义命令行模式执行
define('APP_MODE', 'cli');

// 定义模块名
define('BIND_MODULE', 'CliModule');

// 定义应用目录（linux 下需要写绝对目录）
define('APP_PATH', dirname(__FILE__) . '/Application/');

// 定义网站根目录
define('ROOT_PATH', realpath('./') . DIRECTORY_SEPARATOR);

// 定义根路径
define('ROOT_URL', rtrim(dirname($_SERVER["SCRIPT_NAME"]), '\\/') . '/');

// 网站地址
define('BASE_URL', "http://" . $_SERVER["HTTP_HOST"]);

// 文件上传地址
define('UPLOAD', BASE_URL . '/Public/Upload/');

// 引入 ThinkPHP 入口文件
require dirname(__FILE__) . '/ThinkPHP/ThinkPHP.php';
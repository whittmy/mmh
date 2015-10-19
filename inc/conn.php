<?php
define('MAC_ROOT', substr(__FILE__, 0, -13));

require(MAC_ROOT.'/inc/config/config.php');
require(MAC_ROOT.'/inc/config/cache.php');
require(MAC_ROOT.'/inc/common/class.php');

require(MAC_ROOT.'/inc/common/function.php');
require(MAC_ROOT.'/inc/common/template.php');
require(MAC_ROOT."/inc/common/template_diy.php");

define('MAC_PATH', $MAC['site']['installdir']);
define('MAC_ROOT_TEMPLATE', MAC_ROOT.'/template/'.$MAC['site']['templatedir'].'/'.$GLOBALS['MAC']['site']['htmldir']);
define('MAC_STARTTIME',execTime());
define('MAC_URL','#');
define('MAC_NAME','萌系管理系统');


// 下面的设置部分非常重要， 设置 error_reporting(7)后，所有的警告(如变量不存在，数组指针不存在)信息将不再显示。即不会报错。
//另外设置了 自定义的错误处理函数 my_error_handler,以便统一格式输出管理，当然是符合自己设定的错误等级(如 7)
@session_start();  // session

@header('Content-Type:text/html;Charset=utf-8');
@date_default_timezone_set('Etc/GMT-8');
@ini_set('display_errors','On');
@error_reporting(7);
//@error_reporting(E_ALL);
@set_error_handler('my_error_handler');

//打开缓冲区, 主要解决 header之前有输出会出错的情况。
/* 小例子， 如歌不用 ob_start()，下面会执行出错。 正因为开启了缓冲器而非直接发到浏览器，才能避免出错。
ob_start(); //打开缓冲区
echo \"Hellon\"; //输出
header("location:index.php"); //把浏览器重定向到index.php
ob_end_flush();//输出全部内容到浏览器
*/

@ob_start();
?>
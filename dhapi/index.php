<?php 
//项目所有文件的入口文件
//防跳墙常量 
define('IS_INITPHP','http://api.hongzeit.com');
//关错错误输出
error_reporting(0);
//设置页面字符编码
header("Content-type: text/json; charset=utf-8");
//设置时区
date_default_timezone_set('Asia/Shanghai');

//接入方的URL
//header("Access-Control-Allow-Origin:http://guide.clh.com:81");

//$min_seconds_between_refreshes = 1;#设置刷新的时间
//
//session_start();
//
//if(array_key_exists('last_access', $_SESSION) && time()-$min_seconds_between_refreshes <= $_SESSION['last_access'])
//{ 
//	exit('You are refreshing too quickly, please wait a few seconds and try again.');
//}
//// Record now as their last access time
//$_SESSION['last_access'] = time();
// 
//核心核心
include './SinglePHP.class.php'; 
include './config.php';



SinglePHP::getInstance($config)->run();

?>
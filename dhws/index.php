<?php 
//��Ŀ�����ļ�������ļ�
//����ǽ���� 
define('IS_INITPHP','http://api.hongzeit.com');
//�ش�������
error_reporting(0);
//����ҳ���ַ�����
header("Content-type: text/json; charset=utf-8");
//����ʱ��
date_default_timezone_set('Asia/Shanghai');

//���뷽��URL
//header("Access-Control-Allow-Origin:http://guide.clh.com:81");

//$min_seconds_between_refreshes = 1;#����ˢ�µ�ʱ��
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
//���ĺ���
include './SinglePHP.class.php'; 
include './config.php';



SinglePHP::getInstance($config)->run();

?>
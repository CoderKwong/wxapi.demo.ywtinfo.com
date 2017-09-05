<?php
if (!defined('IS_INITPHP')) exit('Access Denied!'); 

header("content-type:text/html;charset=utf-8");
// ָ������������������
header('Access-Control-Allow-Origin:*');
// ��Ӧ����
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Methods:GET');
// ��Ӧͷ����
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class BaseController extends Controller{
	protected function _init(){ 
		
		//����Services�ļ� 
		includeIfExist(C('APP_FULL_PATH').'/Service/BaseService.class.php');
		includeIfExist(C('APP_FULL_PATH').'/Service/ApiService.class.php');  
		includeIfExist(C('APP_FULL_PATH').'/Service/OtherService.class.php');  
		
		//����dao��common�ļ�    
		includeIfExist(C('APP_FULL_PATH').'/Dao/DatabaseDao.class.php');   
		includeIfExist(C('APP_FULL_PATH').'/Lib/common.class.php');  
		
		//���������ļ�
		include C('APP_FULL_PATH').'/FlowConfig/apiConfig.php';   
		include C('APP_FULL_PATH').'/FlowConfig/otherConfig.php';  
		$flowConfig = array_merge($apiConfig,$otherConfig); 
		C($flowConfig);    
		
		//���� COMMON new ���� 
		$_ENV["commonClass"] = new commonClass();   
		//��������DAO�� 
		$_ENV["dbDao"]	= new DatabaseDao();  
		
		//д��ο� ��־ 
		$note = "IP:".call_user_func(array($_ENV["commonClass"],"ip"))."  �������: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." POST:".iconv("UTF-8","GB2312//IGNORE",file_get_contents("php://input"));
		//$json = json_decode(iconv("GB2312","UTF-8//IGNORE",$json));   
		Log::getpost($note);  
		
	}   
	 
} 

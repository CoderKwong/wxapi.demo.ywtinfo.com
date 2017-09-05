<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
class BaseController extends Controller{
	protected function _init(){ 
		//����Services�ļ� 
		includeIfExist(C('APP_FULL_PATH').'/Service/BaseService.class.php');
		includeIfExist(C('APP_FULL_PATH').'/Service/ApiService.class.php'); 
		includeIfExist(C('APP_FULL_PATH').'/Service/HisService.class.php');
		includeIfExist(C('APP_FULL_PATH').'/Service/OtherService.class.php');
		
		//����dao��common�ļ�    
		includeIfExist(C('APP_FULL_PATH').'/Dao/DatabaseDao.class.php');   
		includeIfExist(C('APP_FULL_PATH').'/Lib/common.class.php');  
		
		//���������ļ�
		include C('APP_FULL_PATH').'/FlowConfig/apiConfig.php';  
		include C('APP_FULL_PATH').'/FlowConfig/hisConfig.php'; 
		include C('APP_FULL_PATH').'/FlowConfig/remoteConfig.php'; 
		include C('APP_FULL_PATH').'/FlowConfig/otherConfig.php'; 
		$flowConfig = array_merge($apiConfig,$hisConfig,$remoteConfig,$otherConfig); 
		C($flowConfig);      
		
		//���� COMMON new ���� 
		$_ENV["commonClass"] = new commonClass();   
		//��������DAO�� 
		$_ENV["dbDao"]	= new DatabaseDao();
		
		//д��ο� ��־ 
		$note = "IP:".call_user_func(array($_ENV["commonClass"],"ip"))."  �������: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		Log::getpost($note);  
		
	}   
	 
} 

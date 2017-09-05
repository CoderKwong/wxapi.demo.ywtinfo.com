<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');    
class ConfigController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	
	//DDPUSH SERVER IP
	public function getServerIpAction(){    
		echo C('AppDDPushIP');   
	}
	
	//APP Version
	public function getAppVersionAction(){    
		echo C('AppVersion');   
	}
	
	//APP Version
	public function getWebVersionAction(){    
		
		$jsonData = array("version"=>C('WebVersion'));
		//jsonp�ص�����������   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; 
		Log::json(json_encode($jsonData));  
		//���ظ�ʽ���лص���û�лص����ַ�ʽ
		if($callback){
			echo $callback . '(' .json_encode($jsonData) .')';  //���ظ�ʽ���ص���jsonp ���� json ����  
		}else{
			echo json_encode($jsonData);  //���ظ�ʽ������ json ����  
		}  
		
	}
	
	

}

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
		//jsonp回调参数，必需   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; 
		Log::json(json_encode($jsonData));  
		//返回格式，有回调或没有回调两种方式
		if($callback){
			echo $callback . '(' .json_encode($jsonData) .')';  //返回格式，回调来jsonp 必需 json 数据  
		}else{
			echo json_encode($jsonData);  //返回格式，必需 json 数据  
		}  
		
	}
	
	

}

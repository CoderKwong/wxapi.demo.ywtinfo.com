<?php
class ApiController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	} 
	
	//调用基本接口
	public function BaseAction($funname) {   
		try{
			//获取传过来参数
			$appUser = $_REQUEST['appUser'];
			$appToken = $_REQUEST['appToken']; 
			  
			//验证是否有值
			if($appUser!="" && $appToken!="" ){		 
				//读出用户是否存
				$appArr =C($appUser); 
				if($appArr){
					//转成JSON
					$json = json_decode($appArr[0]);  
					$appKey = $json->appKey; 
					//判断传过来的TOKEN是否正确
					$boolCheckToken = call_user_func(array($_ENV["commonClass"],"checkToken"),$appKey,$appToken);
					
					if($boolCheckToken){ 
						//判断访问的ACTION是否有权限
						$accessName = $json->accessName;   				    
						$boolCheckAction = call_user_func(array($_ENV["commonClass"],"checkAction"),$accessName,$funname); 
						if($boolCheckAction){ 
							
							//写入参考 日志 
							$note = "IP:".call_user_func(array($_ENV["commonClass"],"ip"))."  传入参数: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
							Log::soaphis($note,$appUser);
							
							//进行最终方式调用
							$apiService = new ApiService(); 
							call_user_func(array($apiService,$funname),$appUser); 
							
						}else{
							call_user_func(array($_ENV["commonClass"],"commErr"),"4");
						}
					}else{  
						echo call_user_func(array($_ENV["commonClass"],"commErr"),"3");
					}		
				}else{ 
					call_user_func(array($_ENV["commonClass"],"commErr"),"2");
				} 
			}else{
				call_user_func(array($_ENV["commonClass"],"commErr"),"1");
			} 
		}catch(Exception $e) { 
			call_user_func(array($_ENV["commonClass"],"commErr"),"5");
		}
	} 
	 
	
	
}

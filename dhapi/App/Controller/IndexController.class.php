<?php
class IndexController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	 
	
	//调用基本接口
	public function BaseAction($funname) {   
		$apiService = new ApiService(); 
		if(!C($funname)){  
			call_user_func(array($_ENV["commonClass"],"urlErr"));
		}else{  
			call_user_func(array($apiService,"ApiEngine"),$funname,C($funname)); 
		}  
		

	} 
}

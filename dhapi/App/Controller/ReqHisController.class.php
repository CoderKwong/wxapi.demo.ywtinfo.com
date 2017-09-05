<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
   
class ReqHisController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	
	
	//调用基本接口
	public function BaseAction($funname) {    
		$hisService = new HisService();  
		if(!C($funname)){ 
			call_user_func(array($_ENV["commonClass"],"urlErr"));
		}else{  
			call_user_func(array($hisService,"HisEngine"),$funname,C($funname)); 
		} 
	} 
	 
	

}

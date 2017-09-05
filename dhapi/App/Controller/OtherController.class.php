<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
   
class OtherController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	 
	//调用基本接口
	public function BaseAction($funname) {   
		$otherService = new OtherService();   
		if(!C($funname)){ 
			call_user_func(array($_ENV["commonClass"],"urlErr"));
		}else{  
			call_user_func(array($otherService,$funname),$funname,C($funname)); 
		} 
	} 
	 
	

}

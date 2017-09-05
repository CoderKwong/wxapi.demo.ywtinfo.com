<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');    

class ApiService extends  BaseService{
	
	//定义每次请求全局变量,返回请求
	private $jsonData =  array();   
	//返回sqlData的数据 
	private $sqlData = null;
	//返回sql执行后,返回boolean，默认为true
	//private $sqlReturn = true;
	//infoString的提示语
	private $infoString = null;
	//检测请求方法，默认是要检测的
	private $boolCheckFun = true;
	//检测用户是否合法性，默认是不合法的
	private	$boolCheckUser = false;   
	//返回结果，如果是LIST 返回DATA不用加入[]
	private	$resultType = null; 
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function ApiEngine($funName,$funObj) {    
		
		 //不用检测的方法列表
		$boolCheckFun = $this->checkFun($funName);   
		if($boolCheckFun){    
			//统一验证方式
			$boolCheckUser = $this->checkUser();  
		}else{ 
			//检测方法过滤通过后进入下一步
			$boolCheckFun = true;
			$boolCheckUser = true; 
		} 
		
		//用于生成返回前台验证时间戳和用户token  
		$jsonData = $this->getDataToken($boolCheckUser);    
		
		//进入流程处理
		if($boolCheckFun && $boolCheckUser){     
			foreach ($funObj as $key => $json) {
				
				//print_r($json);  
				//转码成中文
				//$json = json_decode(iconv("GB2312","UTF-8//IGNORE",$json));     
				$json = json_decode($json);  
				//print_r($json);   
				$nodeType = $json->nodeType;
				$sqlType = $json->sqlType;
				$parameter = $json->parameter;
				$sqlString = $json->sqlString;
				$resultType = $json->resultType;
				$infoString = $json->infoString; 
				
				//先生成sql,把变量转入 
				$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser); 
				//print_r($sqlString); 
				
				
				if($nodeType=="verifi"){ 
					$run = $json->	run; 
					if($run=="pass"){
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);  
					}else if($run=="true" && $sqlData){
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);    
						break;
					}else if($run=="false" && !$sqlData){
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);    
						break;
					}   
				}else{ 
					if($resultType=="boolean"){  
						if(!call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType)){  
							if($nodeType!="pass"){   
								break;
							} 
						}else{
							//如果nodeType是pass验证，验证的数据是false 是可以进入下一步的
							if($nodeType=="pass"){   
								break;
							}  
						}  
					}else if($resultType=="rows"){ 
						$rows = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);  
						
					}else if($resultType=="exesql"){ 
						call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);  
					}else{
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);  
						//print_r($sqlData);
						//如为返回sqlData的数据为空或为false，则变跳转出
						if(!$sqlData){break;} 
					} 
				}
				//print_r($sqlData);   
			} 
		}    
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,$rows,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData);
	} 
	 
}

?>

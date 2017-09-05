<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');    

class HisService extends  BaseService{
	
	//定义每次请求全局变量,返回请求
	private $jsonData =  array();   
	//返回sqlData的数据 
	private $sqlData = null;
	//返回sql执行后,返回boolean，默认为true
	private $xmlData = array();
	private $tempData = array();
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
	public function HisEngine($funName,$funObj) {      
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
		
		//用于生成返回医院HisUrl和Appkey 
		$hisApi = $this->checkHisApi();   
		//进入流程处理
		if($boolCheckFun && $boolCheckUser && $hisApi){      
			//print_r($funObj);  
			foreach ($funObj as $key => $json) {  
				//$json = json_decode(iconv("GB2312","UTF-8//IGNORE",$json));    
				$json = json_decode($json);   
				$nodeType = $json->nodeType; 
				$parameter = $json->parameter;
				$sqlType = $json->sqlType;
				$xmlString = $json->xmlString;
				$resultType = $json->resultType; 
				$dataType = $json->dataType; 
				$sqlString = $json->sqlString; 
				$funcName = $json->funcName; 
				$replaceXmlData = $json->replaceXmlData;  
				$returnXmlData = $json->returnXmlData;  
				$infoString =$json->infoString;   
				$isPass = $json->isPass;  
				
				if($nodeType=="sql"){     
					$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser);  
					$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
					if($isPass=="pass"){ 
						if($sqlData){
							$sqlData="";
							break;
						}  
					}else{ 
						if(!$sqlData){break;} 
					}
					
					if($dataType=="add"){     
						$tempData[] =$sqlData; 
						$sqlData = $tempData =array_merge($tempData, $sqlData); 
					} 
					
					
				}else if($nodeType=="xml"){ 
					//先生成xml,把变量转入 
					$postData= $this->getReplace($parameter,$xmlString,$sqlData,$nodeType,$boolCheckUser); 
					$postUrl = $this->hisApiReplace($hisApi['hisUrl'],$hisApi['appKey'],$funcName); 
					$returnXml = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData); 
					
					//如果为空，则返回HIS的报错
					if(empty($returnXml)){   
						$sqlData =  "";
						$infoString ="服务器连接返回内容出错";
					}else if(strval($returnXml ->resultCode)=="1"){
						$sqlData =  "";
						$infoString = strval($returnXml ->resultDesc);
					}else {
						$sqlData = $this->xmlDataReplace($returnXml,$replaceXmlData,$returnXmlData); 
						if($dataType=="add"){     
							$tempData[] =$sqlData; 
							$sqlData = $tempData =array_merge($tempData, $sqlData);   
						}
					}
					if(!$sqlData){break;}    
				} 
			}  
		}      
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,0,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData); 
	}   

	 
}

?>

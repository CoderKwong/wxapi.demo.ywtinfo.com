<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
class BaseService {
	 
	/**
	 * 用于在检测过滤列表的方法名 
	 * @param string $funName 请求方法名
	 * @return boolean
	 */
	function checkFun($funName)
	{ 
		//print_r($funName);
		$bool=true; 
		$noCheckFun =  C("noCheckFun");
		$json = json_decode($noCheckFun[0]);   
		if (strpos('|'.$json->funName.'|', '|'.$funName.'|') !== false){
			$bool=false;  
		}    
		return $bool;
	} 
	
	
	/**
	* 用于在检测用户的合法性的方法名
	* @param DatabaseDao $dbDao Dao对象
	* @return boolean
	*/
	function checkUser()
	{ 
		$bool=false; 
		$checkUser = C("checkUser"); 
		$json = json_decode($checkUser[0]);      
		$sqlString  = $json->sqlString;
		$parameter  = $json->parameter;  
		$sqlString= $this->getReplace($parameter,$sqlString);    
		$bool = call_user_func(array($_ENV["dbDao"],$json->sqlType),$sqlString,$json->resultType); 
		return $bool;
		//测试用
		//return true;
		
	}
	
	
	/**
	* 用于在检测医院列表接口地址和appKey
	* @param DatabaseDao $dbDao Dao对象
	* @return entity
	*/
	function checkHisApi()
	{  
		$checkHisApi = C("checkHisApi");   
		$json = json_decode($checkHisApi[0]);  
		$sqlString  = $json->sqlString;
		$parameter  = $json->parameter;  
		$sqlString= $this->getReplace($parameter,$sqlString);   
		$entity = call_user_func(array($_ENV["dbDao"],$json->sqlType),$sqlString,$json->resultType); 
		return $entity;
		
	}
	
	
	
	/**
	 * 用于生成返回前台验证时间戳和用户token 
	 * @return json
	 */
	function getDataToken($checkUser) {   
		//验证方案 
		if ($checkUser) {   
			$timestamp=call_user_func(array($_ENV["commonClass"],timestamp));   
			$timestampeq=call_user_func(array($_ENV["commonClass"],timestampeq),$checkUser["timestamp"]);  
			$token=call_user_func(array($_ENV["commonClass"],token), $checkUser['id'],$timestamp);  
			if(((float)$timestamp-(float)$timestampeq)>0){   
				$checkToken = C("updateToken"); 
				$json = json_decode($checkToken[0]); 
				$sqlString  = $json->sqlString;
				$parameter  = $json->parameter;  
				$sqlData = array("timestamp"=>$timestamp,"token"=>$token,"id"=> $checkUser['id']);  
				$sqlString= $this->getReplace($parameter,$sqlString,$sqlData);
				
				$update = call_user_func(array($_ENV["dbDao"],$json->sqlType),$sqlString,$json->resultType);  
				if($update){   
					$jsonData["dataToken"]["timestamp"]=$timestamp; 
					$jsonData["dataToken"]["token"]=$token;     
				}else{  
					$jsonData["dataToken"]["timestamp"]=$checkUser["timestamp"]; 
					$jsonData["dataToken"]["token"]=$checkUser["token"]; 
				}   
			}else{ 
				$jsonData["dataToken"]["timestamp"]=$checkUser["timestamp"]; 
				$jsonData["dataToken"]["token"]=$checkUser["token"];    
			} 
			$jsonData["dataToken"]["status"]="1"; 
			$jsonData["dataToken"]["info"]="验证成功"; 
		}else{    
			$jsonData["dataToken"]["timestamp"]=""; 
			$jsonData["dataToken"]["token"]="";   
			$jsonData["dataToken"]["status"]="0"; 
			$jsonData["dataToken"]["info"]="验证失败"; 
		}    
		return  $jsonData;
		
		//$jsonDataT["dataToken"]["timestamp"]="";
		//$jsonDataT["dataToken"]["token"]=""; 
		//$jsonDataT["dataToken"]["status"]="1"; 
		//$jsonDataT["dataToken"]["info"]="验证成功"; 
		//return  $jsonDataT;
	}
	
	
	
	/**
	 * 用于在获得单个参数 I D
	 * @param string $param 获取参数
	 * @param array $sqlData 传递给子模板的变量列表，key为变量名，value为变量值
	 * @return void
	 */
	function getParameter($parameter,$sqlData,$patientUser)
	{ 
		//print_r($parameter); 
		//print_r($sqlData);
		$parameternew = explode('_', $parameter);
		if($parameternew[0]=="I"){
			return I($parameternew[1],'','htmlspecialchars,trim');
		}else if($parameternew[0]=="D"){ 
			return  $sqlData[$parameternew[1]];
		}else if($parameternew[0]=="M"){  
			return  $patientUser[$parameternew[1]];
		}else if($parameternew[0]=="F"){  
			$parameterF = explode('F_', $parameter); 
			$parameterF = explode('|', $parameterF[1]); 
			return  call_user_func(array($_ENV["commonClass"],$parameternew[1]),$parameterF[1]);
		}else{
			return  null;
		}  
	}
	
	
	/**
	 * 用于在获得代换his api url 替换
	 * @param array $hisUrl hisUrl
	 * @param string $funName 获取请求HIS参数
	 * @param string $appKey 获取appKey
	 * @return void
	 */
	function hisApiReplace($hisUrl,$appKey,$funName)
	{  
		$postUrl = $hisUrl.$funName."&appkey=".md5($appKey."|".date("Y-m-d", time()));  
		return $postUrl;
	}
	
	
	
	/**
	 * 用于在获得代换his api url 替换
	 * @param array $hisUrl hisUrl
	 * @param string $funName 获取请求HIS参数
	 * @param string $appKey 获取appKey
	 * @return void
	 */
	function xmlDataReplace($returnXml,$replaceXmlData,$returnXmlData)
	{      
		$xmlData = array();
		$replaceXmlData = explode(',', $replaceXmlData); 
		if($returnXmlData=="one"){
			foreach ($replaceXmlData as $key => $replaceXml) {     
				$xmlData[$replaceXml] =trim(strval($returnXml->$replaceXml));
			}  
		}else if($returnXmlData=="list"){ 
			foreach($returnXml as $key=>$value){   
				$replaceXmlDataTemp = null; 
				foreach ($replaceXmlData as $key => $replaceXml) {     
					$replaceXmlDataTemp[] =trim($value->$replaceXml);
				}    
				$xmlData[] = array_combine($replaceXmlData,$replaceXmlDataTemp); 
			} 
		}     
	 	return $xmlData;
		
	}
	
	
	/**
	 * 用于在获得代换SQL 
	 * @param array $parameter 传递给参数数组
	 * @param string $sqlString 获取要代换的SQL
	 * @param string $sqlData 获取要代换的Sql得到的实体类数据
	 * @return void
	 */
	function getReplace($parameter,$sqlString,$sqlData,$nodeType,$patientUser)
	{  
		$parameter = explode(',', $parameter);
		foreach ($parameter as $key => $value) {   
			if($nodeType=="if"){
				//过滤if 语句
				$temp= explode('|', $value); 
				if($temp[0]=="IF"){  
					$tempv=$this->getParameter($temp[1],$sqlData,$patientUser); 
					if($tempv){
						//有值，要把<if></if> 整个都加入  
						$sqlString = strip_tags($sqlString);
						$sqlString = str_replace("{".$temp[1]."}",$tempv,$sqlString);  
					}else{
						//无值，要把<if></if> 整个都去掉 
						$sqlString=preg_replace("/<(if.*?)>(.*?)<(\/if.*?)>/si","",$sqlString);
					} 
				}else{				
					$sqlString = str_replace("{".$value."}",$this->getParameter($value,$sqlData,$patientUser),$sqlString); 
				}
			}else{
				$sqlString = str_replace("{".$value."}",$this->getParameter($value,$sqlData,$patientUser),$sqlString); 
			}
		}  
		return $sqlString;
	}
	
	/**
	 * 用于在生成返回dataInfo的数据
	 * @param array $jsonData 返回json数据
	 * @param string $sqlData 数据库返回的数据
	 * @param string $infoString 错误返回的提示
	 * @return void
	 */
	function getJson($jsonData,$sqlData,$infoString,$rows="0",$resultType="list"){   
		if($sqlData){ 
			$jsonData["dataInfo"]["status"]="1"; 
			$jsonData["dataInfo"]["info"]="成功";
			$jsonData["dataInfo"]["rows"]=$rows;
			if($resultType=="list"){
				$jsonData["dataInfo"]["data"]=$sqlData;
			}else{
				$jsonData["dataInfo"]["data"][]=$sqlData;
			}
			
		}else{
			$jsonData["dataInfo"]["status"]="0"; 
			$jsonData["dataInfo"]["rows"]="0";
			$jsonData["dataInfo"]["info"]=$infoString;
			$jsonData["dataInfo"]["data"]="";
		} 
		
		return  $jsonData;
	}
	
	/**
	 * 用于生成返回前台JSON
	 * @param string $path 相对于View目录的路径
	 * @param array $data 传递给子模板的变量列表，key为变量名，value为变量值
	 * @return void
	 */
	function codeJson($jsonData)
	{
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

?>

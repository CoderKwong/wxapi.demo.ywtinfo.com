<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');    

class OtherService extends  BaseService{
	
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
	public function medicineAdd($funName,$funObj) {    
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
		$stepData1 = null;
		$stepData2 = null;
		$stepData3 = null;
		$stepData4 = null;
		$stepData5 = null; 
		
		//进入流程处理
		if($boolCheckFun && $boolCheckUser){ 
			foreach ($funObj as $key => $json) {     
				$json = json_decode($json);   
				$nodeType = $json->nodeType;
				$sqlType = $json->sqlType;
				$parameter = $json->parameter;
				$sqlString = $json->sqlString;
				$resultType = $json->resultType;
				$infoString = $json->infoString;  
				
				
				if($nodeType=="step1"){   
					$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser); 
					$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType); 
					$stepData1 =$sqlData;
				}else if($nodeType=="step2"){
					//添加处方
					if(!$stepData1){   
						$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser); 
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
						$stepData2 =$sqlData;
					} 
				}else if($nodeType=="step3"){
					//添加药品组
					if($stepData1){   
						$sqlString= $this->getReplace($parameter,$sqlString,$stepData1,$nodeType,$boolCheckUser);
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
						$stepData3 =$sqlData;
					}else{ 
						if($stepData2){
							$sqlString= $this->getReplace($parameter,$sqlString,$stepData2,$nodeType,$boolCheckUser);
							$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
							$stepData3 =$sqlData;
						}
					}
				}else if($nodeType=="step4"){
					//添加计划组
					if($stepData3){   
						$sqlString= $this->getReplace($parameter,$sqlString,$stepData3,$nodeType,$boolCheckUser);
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
						$stepData4 =$sqlData;
					}
				}else if($nodeType=="step5"){
					//添加计划
					if($stepData4){    
						$consumptionHoursString = explode(',', $this->getParameter("I_consumptionHoursString")); 
						$quantityString = explode(',', $this->getParameter("I_quantityString")); 
						$startTime = $this->getParameter("I_startDate");
						$doseType = $this->getParameter("I_doseType");
						$drugName = $this->getParameter("I_drugName");
						$customerUserId = $this->getParameter("M_id","",$boolCheckUser);
						$customerFamilyId = $this->getParameter("I_customerFamilyId");
						$data_values=null;
						$data_values1=null;
						for ($x=0; $x<=count($consumptionHoursString)-1; $x++) {
							$data_values .= "('".$stepData4['autoid']."','".$startTime." ".$consumptionHoursString[$x]."','".$startTime." ".$consumptionHoursString[$x]."','pending','".$stepData3['autoid']."','".$consumptionHoursString[$x]."','".$quantityString[$x]."','".$doseType."'),"; 
							$data_values1 .="('".$stepData4['autoid']."','服用','".$drugName.$quantityString[$x].$doseType."','".$customerUserId."','".$customerFamilyId."','2','".$startTime." ".$consumptionHoursString[$x].":00','".date("Y-m-d H:i:s", time())."'),"; 
						}   
						$data_values = substr($data_values,0,-1); //去掉最后一个逗号  
						$data_values1 = substr($data_values1,0,-1); //去掉最后一个逗号  
						$sqlString= $sqlString . $data_values;
						$sqlString1= "INSERT INTO hz_pushtask (parameter,title,content,customerUserId,customerFamilyId,eventType,pushTime,createTime)	VALUES" . $data_values1;
						$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
						call_user_func(array($_ENV["dbDao"],"insert"),$sqlString1,"return");   
						$stepData5 =$sqlData;
						
						
						
						

					}
				}
				
				
			}
		}    
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,$rows,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData);
	} 
	
	
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function registerVerifyCode($funName,$funObj) {    
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
		$sqlString = "SELECT COUNT(1) as rows FROM hz_verifysmscode t WHERE t.phone='{I_phone}' and type=0 AND DATE(t.createtime)=DATE(SYSDATE())";
		$sqlString= $this->getReplace("I_phone",$sqlString,$sqlData,$nodeType,$boolCheckUser);
		$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
		if(intval($rows['rows'])<5){  
			//进入流程处理
			if($boolCheckFun && $boolCheckUser){  
				foreach ($funObj as $key => $json) {      
					$json = json_decode($json);  
					
					$nodeType = $json->nodeType;
					$sqlType = $json->sqlType;
					$parameter = $json->parameter;
					$sqlString = $json->sqlString;
					$resultType = $json->resultType;
					$infoString = $json->infoString;   
					$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser);
					
					$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
					if(!$sqlData){break;}  
				}
				
				//发送短信
				if($sqlData)
				{     
					
					$smsApi =C('SmsApi');
					$smsStr ="【南方医院】".$sqlData['code']."（注册验证码），请在20分钟内完成注册。如非本人操作，请忽略。";
					$url =$smsApi."&mobile=".$sqlData['phone']."&content=".$smsStr."&ext=1"; 
					$result = call_user_func(array($_ENV["commonClass"],"send_get"),$url);    
					$sqlString = "update hz_verifysmscode set  status='$result' where id='".$sqlData['id']."'";
					call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"exesql");    
					$sqlData = array("return"=>"true");
					
				}
			}  
		}else{  
			$infoString = "注册验证码一天只能发5条，请明天再试。";   
		}    
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,$rows,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData);
	} 
	
	
	
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function resetPwdVerifyCode($funName,$funObj) {    
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
		$sqlString = "SELECT COUNT(1) as rows FROM hz_verifysmscode t WHERE t.phone='{I_phone}' and type=1 AND DATE(t.createtime)=DATE(SYSDATE())";
		$sqlString= $this->getReplace("I_phone",$sqlString,$sqlData,$nodeType,$boolCheckUser);
		$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
		
		if(intval($rows['rows'])<5){  
			//进入流程处理
			if($boolCheckFun && $boolCheckUser){  
				foreach ($funObj as $key => $json) {      
					$json = json_decode($json);  
					
					$nodeType = $json->nodeType;
					$sqlType = $json->sqlType;
					$parameter = $json->parameter;
					$sqlString = $json->sqlString;
					$resultType = $json->resultType;
					$infoString = $json->infoString;   
					$sqlString= $this->getReplace($parameter,$sqlString,$sqlData,$nodeType,$boolCheckUser);
					
					$sqlData = call_user_func(array($_ENV["dbDao"],$sqlType),$sqlString,$resultType);   
					if(!$sqlData){break;}  
				}
				
				//发送短信
				if($sqlData)
				{     
					$smsApi =C('SmsApi'); 
					$smsStr ="【南方医院】".$sqlData['code']."（重置密码验证码），请在20分钟内完成重置密码。如非本人操作，请忽略。";
					$url =$smsApi."&mobile=".$sqlData['phone']."&content=".$smsStr."&ext=1"; 
					$result = call_user_func(array($_ENV["commonClass"],"send_get"),$url);    
					$sqlString = "update hz_verifysmscode set  status='$result' where id='".$sqlData['id']."'";
					call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"exesql");   
					
					$sqlData = array("return"=>"true"); 
				} 
			}
			
		}else{  
			$infoString = "重置密码验证码一天只能发5条，请明天再试。";   
		}    
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,$rows,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData);
	} 
	
	
	
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function appointsOrder($funName,$funObj) {    
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
			
			//检测本地有没有卡 
			$sqlString = "SELECT COUNT(1) as rows FROM hz_customercard WHERE hospitalId='{I_hospitalId}'  AND customerFamilyId ='{I_customerFamilyId}'";
			$sqlString= $this->getReplace("I_hospitalId,I_customerFamilyId",$sqlString,$sqlData,$nodeType,$boolCheckUser);
			$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
			if(intval($rows['rows'])>=1)
			{ 
				//一天一个医院只能挂号两条
				$sqlString = "SELECT COUNT(1) as rows FROM hz_appointsorder t WHERE t.hospitalId='{I_hospitalId}' and t.regDate='{I_regDate}' AND t.customerFamilyId='{I_customerFamilyId}' AND t.resultCode='0' AND t.cancelFlag='1' ";
				$sqlString= $this->getReplace("I_hospitalId,I_regDate,I_customerFamilyId",$sqlString,$sqlData,$nodeType,$boolCheckUser);
				$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
				
				if(intval($rows['rows'])<2){  
					
					//一天一个医院只能挂号两条
					$sqlString = "SELECT COUNT(1) as rows FROM hz_appointsorder t WHERE t.hospitalId='{I_hospitalId}' and t.deptId='{I_deptId}' and t.doctorId='{I_doctorId}' and t.regDate='{I_regDate}' AND t.customerFamilyId='{I_customerFamilyId}' AND t.resultCode='0' AND t.cancelFlag='1' ";
					$sqlString= $this->getReplace("I_hospitalId,I_deptId,I_doctorId,I_regDate,I_customerFamilyId",$sqlString,$sqlData,$nodeType,$boolCheckUser);
					$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
					if(intval($rows['rows'])<1){       
						
						foreach ($funObj as $key => $json) {   
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
						
					}else{  
						$infoString = "同个医生当天只能挂1个号";   
					}  
				}else{  
					$infoString = "同个医院当天只能挂2个号";   
				}  
			}else{ 
				//检测本地预约次数1次
				$sqlString = "SELECT COUNT(1) as rows FROM  hz_appointsorder WHERE   hospitalId='{I_hospitalId}'  AND customerFamilyId ='{I_customerFamilyId}' and resultCode='0'";
				$sqlString= $this->getReplace("I_hospitalId,I_customerFamilyId",$sqlString,$sqlData,$nodeType,$boolCheckUser);
				$rows = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
				if(intval($rows['rows'])<1){ 
					//HIS验证用户名
					$sqlString = "SELECT trueName,idNo,address,sex,phone,birthDay,nation FROM hz_customerfamily WHERE id ='{I_customerFamilyId}'";
					$sqlString= $this->getReplace("I_customerFamilyId",$sqlString,$sqlData,$nodeType,$boolCheckUser);
					$data = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");    
					$xmlString ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{D_idNo}</userIdCard><username>{D_trueName}</username></req>";
					$postData= $this->getReplace("I_hospitalId,D_idNo,D_trueName",$xmlString,$data,$nodeType,$boolCheckUser); 
					$postUrl = $this->hisApiReplace($hisApi['hisUrl'],$hisApi['appKey'],"confirmPatient"); 
					$returnXml = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData); 
					
					//如果为空，则返回HIS的报错
					if(empty($returnXml)){    
						$infoString ="服务器连接返回内容出错";
					}else{
						$xmlData = $this->xmlDataReplace($returnXml,"cardId,patientId","one"); 
						
						
						if(!empty($xmlData["cardId"]))
						{ 
							//HIS验证用户名绑定
							$sqlString = "insert into hz_customercard (customerFamilyId,hospitalId,cardType,cardId,patientId,createTime) VALUES ('{I_customerFamilyId}','{I_hospitalId}','1','{D_cardId}','{D_patientId}','{F_timenow}')";
							$sqlString= $this->getReplace("I_customerFamilyId,I_hospitalId,D_cardId,D_patientId,F_timenow",$sqlString,$xmlData,$nodeType,$boolCheckUser);
							$data = call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"entity"); 
							
							foreach ($funObj as $key => $json) {   
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
							
						}else{    
							//发送HIS挂号  
							$funObj = C("appointsOrderNew");      
							foreach ($funObj as $key => $json) {   
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
									
									//如果病人不为空		
									if(empty($xmlData["patientId"])){ 
										//2016年10月31日10:31:12
										//如果卡为空，则进建档方式
										//建档成功后，用户病人ID接口	
										
										//Log::posthis("QuerySchedule:req2\r\n"."*c".call_user_func(array($_ENV["commonClass"],"timestamp")));
										$xmlPatString ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{D_idNo}</userIdCard><username>{D_trueName}</username><sex>{D_sex}</sex><birthDay>{D_birthDay}</birthDay><phone>{D_phone}</phone><nation>{D_nation}</nation><address>{D_address}</address></req>";
										$postData= $this->getReplace("I_hospitalId,D_idNo,D_trueName,D_sex,D_birthDay,D_phone,D_nation,D_address",$xmlPatString,$data,$nodeType,$boolCheckUser); 
										$postUrl = $this->hisApiReplace($hisApi['hisUrl'],$hisApi['appKey'],"createPatient"); 
										$returnXml = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);  
										if(empty($returnXml)){   
											$sqlData =  "";
											$infoString ="服务器连接返回内容出错";
										}else if(strval($returnXml ->resultCode)=="1"){
											$sqlData =  "";
											$infoString = strval($returnXml ->resultDesc); 
										}else {
											//把病人ID加入 
											$tempPatData= array("patientId" => $returnXml ->patientId );    
											$tempData[] =$sqlData; 
											$sqlData = $tempData =array_merge($tempData, $tempPatData);  
										} 
										
									}else{ 
										//把病人ID加入 
										$tempPatData = array("patientId" => $xmlData["patientId"]); 
										$tempData[] =$sqlData; 
										$sqlData = $tempData =array_merge($tempData, $tempPatData);   
									}  
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
										$tempPatData = $this->xmlDataReplace($returnXml,$replaceXmlData,$returnXmlData); 
										if($dataType=="add"){      
											$tempData[] =$sqlData; 
											$sqlData = $tempData =array_merge($tempData, $tempPatData); 
										}
									}
									if(!$sqlData){break;}    
								} 
							}  
						}   
					}
					
					
				}else{
					$infoString = "非首诊须绑定诊疗卡";   
				} 
			}  
			
		}
		//最终生成数据
		$jsonData= $this->getJson($jsonData,$sqlData,$infoString,0,$resultType);
		//封装后，返回前台json包
		$this->codeJson($jsonData); 
	} 
	
	
	
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function getDoctorBookingScheduleSyn($funName,$funObj) {      
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
						
						/*  原来的
							foreach($returnXml as $key=>$ri){   
							$TimeRegInfoList = $ri->TimeRegInfoList;
							foreach($TimeRegInfoList as $key=>$tril){   
								$timeRegInfo = $tril->timeRegInfo;    
								$replaceXmlDataTemp = null; 
								foreach($timeRegInfo as $key=>$tri){      
									$sqlData[] = array(
										'hospitalId'		=> I("hospitalId"), 
										'deptId'		=> I("deptId"), 
										'doctorId'		=> strval($ri->doctorId), 
										'regDate'		=> strval($tril->regDate), 
										'regWeekDay'		=> strval($tril->regWeekDay), 
										'timeFlag'		=> strval($tri->timeFlag), 
										'regTotalCount'		=> strval($tri->regTotalCount), 
										'regLeaveCount'		=> strval($tri->regleaveCount), 
										'regFee'		=> strval($tri->regFee), 
										'treatFee'		=> strval($tri->treatFee)  
										);  
								} 
							} 
						}  
						*/ 
						$Schedule = $returnXml->Schedules->Schedule; 
						foreach($Schedule as $key=>$v){   
							if(strval($v->ScheduleStatus)=="N" || strval($v->ScheduleStatus)=="A"){
								
								$timeFlag = "";
								if(strval($v->SessionCode)=="S")
								{
									$timeFlag ="1";
								}else  if(strval($v->SessionCode)=="X"){
									$timeFlag ="2";
								}else  if(strval($v->SessionCode)=="W"){
									$timeFlag ="3";
								}
								
								
								
								$sqlData[] = array(
									'hospitalId'		=> I("hospitalId"), 
									'deptId'		=> I("deptId"), 
									'doctorId'		=> strval($v->DoctorCode), 
									'regDate'		=> strval($v->ServiceDate), 
									'regWeekDay'		=> strval($v->WeekDay), 
									'timeFlag'		=> $timeFlag, 
									'regTotalCount'		=>  strval($v->AvailableTotalNum), 
									'regLeaveCount'		=> strval($v->AvailableLeftNum), 
									'regFee'		=> (float)(strval($v->RegFee))*100, 
									'treatFee'		=> (float)(strval($v->CheckupFee))*100  
									);  
							}
						} 
						
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
	
	
	
	/**
	* 用于在Api引擎中解析数组列表方式
	* @param string $funName 方式名
	* @param array $funOjb 数组列表
	* @return json
	*/
	public function getGuideList($funName,$funObj) {      
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
						$sqlData =  "";
					   $returnXml = $returnXml->invoiceList->invoice;
						foreach($returnXml as $key=>$v){   
							$sqlData[] = array(
								'invoice'		=> $v
							);
						}  
						 
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

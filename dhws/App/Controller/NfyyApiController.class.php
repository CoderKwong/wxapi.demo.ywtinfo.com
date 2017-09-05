<?php 
if (!defined('IS_INITPHP')) exit('Access Denied!');

class NfyyApiController extends BaseController { 
	
	
	//科室信息查询接口
	//<req><hospitalId>1051</hospitalId><deptId/></req> 
	public function getDeptInfoAction(){    
		//==============获得前台请求==============
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象
		
		
		//==============封装HIS请求==============
		$TradeCode = "1012";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = ""; 
		$HospitalId = ""; 
		$DepartmentType = ""; 
		$DepartmentCode = $req->deptId;   
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><DepartmentType>$DepartmentType</DepartmentType><DepartmentCode>$DepartmentCode</DepartmentCode><ExtUserID>$ExtUserID</ExtUserID></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryDepartment&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getDoctorInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getDoctorInfo:res\r\n".$res);   
		$res = simplexml_load_string($res);
		
		//==============处理返回==============   
		$refxml = ""; 
		$ParentId = "";
		
		$xmldata = $res->Departments->Department; 
		
		$refxml .= "<res>"; 
		//$refxml .= "<resultCode>".$res->ResultCode."</resultCode><resultDesc>".$res->ResultContent."</resultDesc>";  
		
		//如果为空，则分组出来 
		//提取相同的分组列表
		$arr = array();
		foreach($xmldata as $key=>$v){      
			if($v->ParentId != $ParentId){  
				$arr[] =$v->ParentId;
				$ParentId = $v->ParentId;
			}
		}
		 
		$arr=array_unique($arr); 
		
		foreach($arr as $key=>$v){      
			
			$refxml .="<deptInfo><deptId>".$v."</deptId><deptName>".$v."</deptName><parentId>-1</parentId><visitAddress></visitAddress><desc></desc></deptInfo>";			
		}  
		
		foreach($xmldata as $key=>$v){      
			$refxml .="<deptInfo><deptId>".$v->DepartmentCode."</deptId><deptName>".$v->DepartmentName."</deptName><parentId>".$v->ParentId."</parentId><visitAddress>".$v->DepartmentAddress."</visitAddress><desc>".$v->Description."</desc></deptInfo>";			
		}  
		$refxml .= "</res>";
		echo $refxml; 
		
	}
	
	
	//科室信息查询接口
	//<req><hospitalId>1051</hospitalId><deptId/></req> 
	public function getDoctorInfoAction(){    
		//==============获得前台请求==============
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象
		
		
		//==============封装HIS请求============== 
		$TradeCode = "1013";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = "";
		$HospitalId = "";
		$DoctorName =  $req->doctorId;
		$DepartmentCode =  $req->deptId;
		
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><DepartmentCode>$DepartmentCode</DepartmentCode><DoctorName>$DoctorName</DoctorName></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryDoctor&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getDoctorInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getDoctorInfo:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回==============    
		$refxml = "";  
		
		$xmldata = $res->Doctors->Doctor; 
		
		$refxml .= "<res>"; 
		$refxml .= "<resultCode>".$res->ResultCode."</resultCode><resultDesc>".$res->ResultContent."</resultDesc>";  
		
		foreach($xmldata as $key=>$v){      
			$refxml .="<doctorInfo><doctorId>".$v->DoctorCode."</doctorId><doctorName>".$v->DoctorName."</doctorName><deptId>".$v->DepartmentCode."</deptId><Title>".$v->DoctorTitleCode."</Title><Fee>100</Fee><Gender></Gender><Desc>".$v->DoctorSpec."</Desc></doctorInfo>";
		}  
		$refxml .= "</res>";
		
		echo $refxml; 
		
	}
	
	
	//医生号源信息查询接口
	//<req><hospitalId>1051</hospitalId><deptId>210101</deptId><doctorId>0436</doctorId><startDate>2013-03-22</startDate><endDate>2013-03-29</endDate></req>
	public function getRegInfoAction(){    
		//==============获得前台请求==============
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象
		
		$TradeCode = "1004";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType =""; 
		$HospitalId =""; 
		$DeptType = ""; 
		$DoctorCode = $req->doctorId; 
		$SessType = ""; 
		$StartDate = $req->startDate; 
		$EndDate = $req->endDate; 
		$RBASSessionCode = ""; 
		$ServiceCode = ""; 
		$StopScheduleFlag = ""; 
		$DepartmentCode = $req->deptId;
		$SearchCode = "";   
		
		
		$postData = "<Request><HospitalId>$HospitalId</HospitalId><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ExtUserID>$ExtUserID</ExtUserID><ClientType>$ClientType</ClientType><TradeCode>$TradeCode</TradeCode><DeptType>$DeptType</DeptType><DoctorCode>$DoctorCode</DoctorCode><SessType>$SessType</SessType><StartDate>$StartDate</StartDate><EndDate>$EndDate</EndDate><RBASSessionCode>$RBASSessionCode</RBASSessionCode><ServiceCode>$ServiceCode</ServiceCode><StopScheduleFlag>$StopScheduleFlag</StopScheduleFlag><DepartmentCode>$DepartmentCode</DepartmentCode><SearchCode>$SearchCode</SearchCode></Request>";		
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QuerySchedule&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getRegInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getRegInfo:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回============== 
		//ScheduleStatus  N 才可以预约 没有过滤
		$refxml = "";   
		$refxml .= "<res>"; 
		$refxml .= "<resultCode>".$res->ResultCode."</resultCode><resultDesc>".$res->ResultContent."</resultDesc>";  
		$refxml .= $res->Schedules->asXML();     
		$refxml .= "</res>";
		
		echo $refxml; 
		
	}
	
	
	
	//医生号源分时信息查询接口
	//<req><hospitalId>1051</hospitalId><deptId>7034229</deptId><doctorId>446</doctorId><regDate>2013-03-29</regDate><timeFlag>1</timeFlag></req>
	public function getTimeRegInfoAction(){    
		//==============获得前台请求==============
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象
		
		
		$TradeCode = "10041";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = ""; 
		$HospitalId = "";  
		$RBASSessionCode = $req->timeFlag; 
		$ScheduleItemCode = "";   
		$DepartmentCode = $req->deptId; 
		$DoctorCode = $req->doctorId; 
		$ServiceDate = $req->regDate;   
		
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><DepartmentCode>$DepartmentCode</DepartmentCode><DoctorCode>$DoctorCode</DoctorCode><RBASSessionCode>$RBASSessionCode</RBASSessionCode><ScheduleItemCode>$ScheduleItemCode</ScheduleItemCode><ServiceDate>$ServiceDate</ServiceDate></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryScheduleTimeInfo&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getTimeRegInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getTimeRegInfo:res\r\n".$res);   
		$res = simplexml_load_string($res);
		
		//==============处理返回============== 
		$refxml = "";   
		$refxml .= "<res>"; 
		//$refxml .= "<resultCode>".$res->ResultCode."</resultCode><resultDesc>".$res->ResultContent."</resultDesc>"; 
		$xmldata = $res->TimeRanges->TimeRange;
		
		foreach($xmldata as $key=>$v){      
			$refxml .="<timeRegInfo><scheduleCode>".$v->ScheduleItemCode."</scheduleCode><startTime>".$v->StartTime."</startTime><endTime>".$v->EndTime."</endTime><regTotalCount>".$v->AvailableTotalNum."</regTotalCount><regLeaveCount>".$v->AvailableLeftNum."</regLeaveCount></timeRegInfo>";
		} 
		$refxml .= "</res>";
		
		echo $refxml; 
		
	}
	
	
	
	//医院用户信息绑定就诊卡接口
	//<req><hospitalId>1051</hospitalId><userIdCard>440507XXXXXXXX0021</userIdCard><username>XXX</username></req> 
	public function confirmPatientAction(){   
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		
		//==============封装HIS请求==============
		$TradeCode ="3300";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT"; 
		$ClientType = ""; 
		$HospitalId = "";    
		$TransactionId = ""; 
		$TerminalID = ""; 
		$PatientCard = "";  
		$CardType = "";  
		$Phone = "";  
		$IDCardType = "02"; 
		$PatientID = "";  
		$IDNo =  $req->userIdCard;    
		$PatientName = $req->username;  

		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><TransactionId>$TransactionId</TransactionId><TerminalID>$TerminalID</TerminalID><PatientCard>$PatientCard</PatientCard><CardType>$CardType</CardType><PatientID>$PatientID</PatientID><Phone>$Phone</Phone><IDCardType>$IDCardType</IDCardType><IDNo>$IDNo</IDNo><PatientName>$PatientName</PatientName></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=GetPatInfo&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("confirmPatient:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("confirmPatient:res\r\n".$res);   
		
		try{
			
			$res = simplexml_load_string($res);
			
			//==============处理返回==============    
			$refxml = "";  
			
			if($res->ResultCode=="0")
			{ 
				$refxml ="<res><resultCode>0</resultCode><resultDesc>用户存在</resultDesc><patientId>".$res->PatientID."</patientId><cardId>".$res->CardNo."</cardId><phone>".$res->Mobile."</phone><isOk>1</isOk></res>";			
			}else{ 
				$refxml ="<res><resultCode>1</resultCode><resultDesc>输入的身份证号和姓名与医院登记不符</resultDesc><patientId></patientId><cardId></cardId><phone></phone><isOk>0</isOk></res>";			
			}
		}catch(Exception $e) {
			$refxml ="<res><resultCode>1</resultCode><resultDesc>输入的身份证号和姓名与医院登记不符</resultDesc><patientId></patientId><cardId></cardId><phone></phone><isOk>0</isOk></res>";			
		}
		
		
		echo $refxml;  		 
		
	}
	
	
	
	//医院用户信息绑定就诊卡接口
	//<req><hospitalId>1051</hospitalId><patientId>440507XXXXXXXX0021</patientId><userCard>4401000000000000</userCard></req> 
	public function cardMoneyAction(){   
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		
		//==============封装HIS请求==============
		$TradeCode ="3300";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT"; 
		$ClientType = ""; 
		$HospitalId = "";    
		$TransactionId = ""; 
		$TerminalID = ""; 
		$PatientCard = "";  
		$CardType = "";  
		$Phone = "";  
		$IDCardType = ""; 
		$PatientID = $req->patientId;  
		$IDNo =  "";    
		$PatientName = "";  

		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><TransactionId>$TransactionId</TransactionId><TerminalID>$TerminalID</TerminalID><PatientCard>$PatientCard</PatientCard><CardType>$CardType</CardType><PatientID>$PatientID</PatientID><Phone>$Phone</Phone><IDCardType>$IDCardType</IDCardType><IDNo>$IDNo</IDNo><PatientName>$PatientName</PatientName></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=GetPatInfo&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("cardMoney:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("cardMoney:res\r\n".$res);   
		
		try{
			
			$res = simplexml_load_string($res);
			
			
			
			//==============处理返回==============    
			$refxml = "";  
			
			if($res->ResultCode=="0")
			{ 
				$refxml ="<res><resultCode>0</resultCode><resultDesc>查询成功</resultDesc><money>".$res->ParentlifeAccount."</money><isOk>1</isOk></res>";			
			}else{ 
				$refxml ="<res><resultCode>1</resultCode><resultDesc>$res->ResultContent</resultDesc><money></money><isOk>0</isOk></res>";			
			}
		}catch(Exception $e) {
			$refxml ="<res><resultCode>1</resultCode><resultDesc>系统出错</resultDesc><money></money><isOk>0</isOk></res>";			
		}
		
		echo $refxml;  		  
	}
	
	
	
	//医院用户信息绑定就诊卡接口
	//<req> <hospitalId>1051</hospitalId><userIdCard>440507XXXXXXXX0021</userIdCard><username>XXX</username><gender>F</gender><userCard>4401000000000000</userCard></req> 
	public function confirmCardAction(){   
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		
		//==============封装HIS请求==============
		$TradeCode ="3300";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT"; 
		$ClientType = ""; 
		$HospitalId = "";    
		$TransactionId = ""; 
		$TerminalID = ""; 
		$PatientCard = $req->userCard;  
		$CardType = "02";  
		$Phone = "";  
		$IDCardType = ""; 
		$PatientID = "";  
		$IDNo =  $req->userIdCard;    
		$PatientName = $req->username;  

		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><TransactionId>$TransactionId</TransactionId><TerminalID>$TerminalID</TerminalID><PatientCard>$PatientCard</PatientCard><CardType>$CardType</CardType><PatientID>$PatientID</PatientID><Phone>$Phone</Phone><IDCardType>$IDCardType</IDCardType><IDNo>$IDNo</IDNo><PatientName>$PatientName</PatientName></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=GetPatInfo&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("confirmCard:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("confirmCard:res\r\n".$res);
		
		try{   
			$res = simplexml_load_string($res);
			
			
			//==============处理返回==============    
			$refxml = "";  
			
			if($res->ResultCode=="0")
			{ 
				if($PatientName==strval($res->PatientName)){ 
					$refxml ="<res><resultCode>0</resultCode><resultDesc>用户存在</resultDesc><patientId>".$res->PatientID."</patientId><isOk>1</isOk></res>";			
				}else{
					$refxml ="<res><resultCode>1</resultCode><resultDesc>姓名或身份证与医院信息不对，请到医院补录后，再绑定</resultDesc><patientId></patientId><isOk>0</isOk></res>";			
				}
			}else{ 
				$refxml ="<res><resultCode>1</resultCode><resultDesc>病人信息不存在或信息不全，请到医院补录后，再绑定</resultDesc><patientId></patientId><isOk>0</isOk></res>";			
			}
		}catch(Exception $e) {
			$refxml ="<res><resultCode>1</resultCode><resultDesc>系统出错</resultDesc><money></money><isOk>0</isOk></res>";			
		}
		
		echo $refxml;  		  
	} 
	
	//<req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{D_idNo}</userIdCard><username>{D_trueName}</username><sex>{D_sex}</sex><birthDay>{D_birthDay}</birthDay><phone>{D_phone}</phone><nation>{D_nation}</nation></req>
	//新建病人主索引（3014，3011）
	public function createPatientAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		$TradeCode = "3014";
		$ExtUserID = "NFYWT";   
		$PatientType = "07";
		$PatientName = $req->username;
		$Sex = (strval($req->sex) == "男" ? "1":"2"); 
		$DOB = $req->birthDay;
		$MaritalStatus = ""; 
		$Nation = ""; 
		$Occupation = ""; 
		$Nationality = ""; 
		$IDType ="01";
		$IDNo = $req->userIdCard;
		$Address = $req->address;
		$AddressLocus = "";
		$Zip =""; 
		$Company = "";
		$CompanyAddr = "";
		$CompanyZip = "";
		$CompanyTelNo = "";
		$TelephoneNo = "";
		$Mobile = $req->phone;
		$ContactName = "";
		$ContactAddress = "";
		$Relation = "";
		$Zip = "";
		$ContactTelNo = "";
		$InsureCardFlag = "";
		$InsureCardNo= "";
		
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtUserID>$ExtUserID</ExtUserID><PatientType>$PatientType</PatientType><PatientName>$PatientName</PatientName><Sex>$Sex</Sex><DOB>$DOB</DOB><MaritalStatus>$MaritalStatus</MaritalStatus><Nation>$Nation</Nation><Occupation>$Occupation</Occupation><Nationality>$Nationality</Nationality><IDType>$IDType</IDType><IDNo>$IDNo</IDNo><Address>$Address</Address><AddressLocus>$AddressLocus</AddressLocus><Zip>$Zip</Zip><Company>$Company</Company><CompanyAddr>$CompanyAddr</CompanyAddr><CompanyZip>$CompanyZip</CompanyZip><CompanyTelNo>$CompanyTelNo</CompanyTelNo><TelephoneNo>$TelephoneNo</TelephoneNo><Mobile>$Mobile</Mobile><ContactName>$ContactName</ContactName><ContactAddress>$ContactAddress</ContactAddress><Relation>$Relation</Relation><ContactTelNo>$ContactTelNo</ContactTelNo><InsureCardFlag>$InsureCardFlag</InsureCardFlag><InsureCardNo>$InsureCardNo</InsureCardNo></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=SavePatientCard&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("createPatient:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("createPatient:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回==============    
		$refxml .= "<res>";
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc><patientId>".$res->PatientID."</patientId>";
		$refxml .= "</res>";
		echo $refxml;  		  	
	}
	
	
	//<req><orderId>1477445948625197</orderId><hospitalId>1000</hospitalId><scheduleCode>1803</scheduleCode><deptId>425</deptId><doctorId>1803</doctorId>
	//<regDate>2016-10-27</regDate><timfFlag>S</timfFlag><startTime>08:30</startTime><endTime>09:00</endTime><userIdCard>445122198607155233</userIdCard>
	//<userName>陈礼洪</userName><userAddress></userAddress><userGender>M</userGender><userMobile>18011770183</userMobile><userBirthday>1986-07-15</userBirthday>
	//<operIdCard>445122198607155233</operIdCard><operName>陈礼洪</operName><operMobile>陈礼洪</operMobile><patType>0</patType><patCardId>33043014</patCardId>
	//<orderTime>2016-10-26 09:39:07</orderTime><fee>100</fee><treatfee>300</treatfee></req>
	//预约（1000）
	public function addOrderAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//==============封装HIS请求==============
		$TradeCode = "1000";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = ""; 
		$HospitalId =""; 
		$TransactionId = $req->orderId; 
		$ScheduleItemCode = $req->scheduleCode; 
		$CardType = "02"; 
		$CredTypeCode ="01"; 
		$IDCardNo =$req->userIdCard; 
		$TelePhoneNo = ""; 
		$MobileNo = $req->userMobile; 
		$PatientName = $req->userName;  
		$PayFlag =""; 
		$PayModeCode =""; 
		$PayBankCode = ""; 
		$PayCardNo =""; 
		$PayFee =strval(((float)(strval($req->RegFee))/100)+((float)(strval($req->CheckupFee))/100)); 
		$PayInsuFee =""; 
		$PayInsuFeeStr=""; 
		$PayTradeNo =""; 
		$LockQueueNo =""; 
		$Gender =""; 
		$Address =""; 
		$HISApptID =""; 
		$SeqCode =""; 
		$AdmitRange =""; 
		$StartTime =$req->startTime; 
		$EndTime =$req->endTime;
		$PatientID = $req->patCardId;
		
		
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><TransactionId>$TransactionId</TransactionId><ScheduleItemCode>$ScheduleItemCode</ScheduleItemCode><CardNo>$CardNo</CardNo><CardType>$CardType</CardType><CredTypeCode>$CredTypeCode</CredTypeCode><IDCardNo>$IDCardNo</IDCardNo><TelePhoneNo>$TelePhoneNo</TelePhoneNo><MobileNo>$MobileNo</MobileNo><PatientName>$PatientName</PatientName><PayFlag>$PayFlag</PayFlag><PayModeCode>$PayModeCode</PayModeCode><PayBankCode>$PayBankCode</PayBankCode><PayCardNo>$PayCardNo</PayCardNo><PayFee>$PayFee</PayFee><PayInsuFee>$PayInsuFee</PayInsuFee><PayInsuFeeStr>$PayInsuFeeStr</PayInsuFeeStr><PayTradeNo>$PayTradeNo</PayTradeNo><LockQueueNo>$LockQueueNo</LockQueueNo><Gender>$Gender</Gender><Address>$Address</Address><HISApptID>$HISApptID</HISApptID><SeqCode>$SeqCode</SeqCode><AdmitRange>$AdmitRange</AdmitRange><StartTime>$StartTime</StartTime><EndTime>$EndTime</EndTime><PatientID>$PatientID</PatientID></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=BookService&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("addOrder:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("addOrder:res\r\n".$res);   
		$res = simplexml_load_string($res);  
		
		//==============处理返回==============    
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc><orderIdHIS>".$res->OrderCode."</orderIdHIS><seqCode>".$res->SeqCode."</seqCode><regFee>".$res->RegFee."</regFee><admitRange>".$res->AdmitRange."</admitRange><admitAddress>".$res->AdmitAddress."</admitAddress><orderContent>".$res->OrderContent."</orderContent><transactionId>".$res->TransactionId."</transactionId>";
		$refxml .= "</res>";
		echo $refxml;  		   
	}
	
	//<req><hospitalId>1051</hospitalId><orderId>91303204249270</orderId><orderIdHis>91303204249270</orderIdHis><cancelTime>2013-03-22 10:50:06</cancelTime><cancelReason>临时有事</cancelReason></req> 
	public function cancelOrderAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//<Request><TradeCode>1108</TradeCode><ExtOrgCode>南方医务通</ExtOrgCode><ClientType></ClientType><HospitalId></HospitalId><ExtUserID>NFYWT</ExtUserID><TransactionId></TransactionId><OrderCode>2700||599||1</OrderCode></Request>
		
		//==============封装HIS请求==============
		$TradeCode ="1108";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = ""; 
		$HospitalId = "";    
		$TransactionId =$req->orderId; 
		$OrderCode =$req->orderIdHis;
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><TransactionId>$TransactionId</TransactionId><OrderCode>$OrderCode</OrderCode></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=CancelOrder&Input=";
		$result = file_get_contents($wsdl.$postData);     
		Log::soaphis("cancelOrder:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("cancelOrder:res\r\n".$res);   
		$res = simplexml_load_string($res);  
		
		//==============处理返回==============    
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
		$refxml .= "</res>";
		echo $refxml;  	
	}
	
	
	///   <req>
	///     <hospitalId>1051</hospitalId><orderId>91303224689772</orderId><orderIdHis>91303224689772</orderIdHis><patientId>91303224689772</patientId><orderIdPAY>91303224689772</orderIdPAY>
	///     <payCardNum>002373</payCardNum><payAmout>700</payAmout>
	///     <payMode>4</payMode><payTime>2013-03-22 10:57:43</payTime><payRespCode>0</payRespCode><payRespDesc/>
	///  </req>
	//病人取号确认（2001）
	public function payOrderAction(){  
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//<Request><TradeCode>1108</TradeCode><ExtOrgCode>南方医务通</ExtOrgCode><ClientType></ClientType><HospitalId></HospitalId>
		//<ExtUserID>NFYWT</ExtUserID><OrgHISTradeNo></OrgHISTradeNo><PayCardNo></PayCardNo><RevTranFlag>0</RevTranFlag><BankDate></BankDate>
		//<BankAccDate></BankAccDate><PayModeCode>CPP</PayModeCode><TransactionId></TransactionId><BankTradeNo></BankTradeNo>
		//<PayDate>2016-09-23</PayDate><PayTime>20160923114046</PayTime><PayTradeStr></PayTradeStr><OrderCode>23||223||1</OrderCode>
		//<BankCode></BankCode><ResultCode></ResultCode><OrgPaySeqNo></OrgPaySeqNo><PayInsuFeeStr></PayInsuFeeStr><PayAmt></PayAmt>
		//<ResultContent></ResultContent><PatientID>33043014</PatientID><PayOrderId>20160923114046</PayOrderId></Request>
		
		//==============封装HIS请求==============  
		$TradeCode = "1108";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = "";
		$HospitalId = "";
		$OrderCode = $req->orderIdHis;
		$PatientID = $req->patientId;
		$PayOrderId = "";
		$PayAmt = strval((float)(strval($req->payAmout))/100);
		$PayModeCode = "CPP"; 
		$OrgHISTradeNo = "";
		$PayCardNo= "";
		$RevTranFlag = "";
		$BankDate = "";
		$BankAccDate = "";
		$TransactionId = $req->orderId;
		$BankTradeNo= "";
		$PayDate = "";
		$PayTime = $req->payTime;
		$PayTradeStr = "";
		$BankCode = "";
		$OrgPaySeqNo = "";
		$PayInsuFeeStr = "";
		$ResultContent = "";
		$PayOrderId = "";
		$PayTradeNo = $req->orderIdPAY;
		
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><OrgHISTradeNo>$OrgHISTradeNo</OrgHISTradeNo><PayCardNo>$PayCardNo</PayCardNo><RevTranFlag>$RevTranFlag</RevTranFlag><BankDate>$BankDate</BankDate><BankAccDate>$BankAccDate</BankAccDate><PayModeCode>$PayModeCode</PayModeCode><TransactionId>$TransactionId</TransactionId><BankTradeNo>$BankTradeNo</BankTradeNo><PayDate>$PayDate</PayDate><PayTime>$PayTime</PayTime><PayTradeStr>$PayTradeStr</PayTradeStr><OrderCode>$OrderCode</OrderCode><BankCode>$BankCode</BankCode><ResultCode>$ResultCode</ResultCode><OrgPaySeqNo>$OrgPaySeqNo</OrgPaySeqNo><PayInsuFeeStr>$PayInsuFeeStr</PayInsuFeeStr><PayAmt>$PayAmt</PayAmt><PayTradeNo>$PayTradeNo</PayTradeNo><ResultContent>$ResultContent</ResultContent><PatientID>$PatientID</PatientID><PayOrderId>$PayOrderId</PayOrderId></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=OPAppArrive&Input="; 
		$result = file_get_contents($wsdl.$postData);    
		
		Log::soaphis("payOrder:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("payOrder:res\r\n".$res);   
		$res = simplexml_load_string($res);  
		//==============处理返回==============    
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc><seqCode>".$res->SeqCode."</seqCode><clinicCode>".$res->AdmNo."</clinicCode>";
		$refxml .= "</res>";
		echo $refxml;  	
	}
	// 
	//904  116
	
	///  <req>
	///     <hospitalId>1051</hospitalId><orderId>91303204353987</orderId><patientId>91303224689772</patientId><clinicCode>91303224689772</clinicCode><orderIdPAY>91303204353987</orderIdPAY><returnFee>400</returnFee>
	///     <returnTime>2013-03-22 10:46:53</returnTime><returnReason>没有时间</returnReason><payRespCode>00</payRespCode><payRespDesc/>
	///  </req> 
	//退号（1003）
	public function returnPayAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//<Request><TradeCode>1108</TradeCode><ExtOrgCode>南方医务通</ExtOrgCode><ClientType></ClientType><HospitalId></HospitalId>
		//<ExtUserID>NFYWT</ExtUserID><TransactionId></TransactionId><RefundType>TF</RefundType><AdmNo>299</AdmNo><PayModeCode>CPP</PayModeCode>
		//<PayAmt>44</PayAmt><PayOrderId></PayOrderId></Request>
		
		//==============封装HIS请求============== 
		$TradeCode = "1108";
		$ExtOrgCode = "南方医务通"; 
		$ExtUserID = "NFYWT";  
		$ClientType = ""; 
		$HospitalId = "";
		$TransactionId =  $req->orderId;
		$AdmNo = $req->clinicCode;
		$RefundType = "TF"; 
		$BankCode = "";
		$BankDate = "";
		$BankTradeNo = "";
		$ResultCode = "";
		$ResultContent = "";
		$PayCardNo = "";
		$BankAccDate = "";
		$RevTranFlag = "";
		$PatientID = $req->patientId;
		$PayAmt = "";
		$OrgHISTradeNo = "";
		$OrgPaySeqNo = ""; 
		$PayOrderId = $req->orderIdPay;
		$PayAmt = strval((float)(strval($req->returnFee))/100);
		$PayModeCode = "CPP";
		
		
		//================调用自己WS===============
		$postData = "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><AdmNo>$AdmNo</AdmNo><TransactionId>$TransactionId</TransactionId><RefundType>$RefundType</RefundType><BankCode>$BankCode</BankCode><BankDate>$BankDate</BankDate><BankTradeNo>$BankTradeNo</BankTradeNo><ResultCode>$ResultCode</ResultCode><ResultContent>$ResultContent</ResultContent><PayCardNo>$PayCardNo</PayCardNo><BankAccDate>$BankAccDate</BankAccDate><RevTranFlag>$RevTranFlag</RevTranFlag><PatientID>$PatientID</PatientID><PayAmt>$PayAmt</PayAmt><OrgHISTradeNo>$OrgHISTradeNo</OrgHISTradeNo><OrgPaySeqNo>$OrgPaySeqNo</OrgPaySeqNo><PayOrderId>$PayOrderId</PayOrderId><PayTime>$PayTime</PayTime><PayModeCode>$PayModeCode</PayModeCode></Request>";
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=CancelReg&Input=";
		$result = file_get_contents($wsdl.$postData);     
		
		
		Log::soaphis("returnPay:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("returnPay:res\r\n".$res);   
		$res = simplexml_load_string($res);   
		
		//==============处理返回==============    
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
		$refxml .= "</res>";
		echo $refxml;    		
	} 
	
	
	//<req><patientId>91303204391491</patientId><hospitalId>1051</hospitalId></req>  
	//最近一次就诊日期
	public function getLastClinicDateAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//<Request><TradeCode>1104</TradeCode><ExtOrgCode>南方医务通</ExtOrgCode><ClientType></ClientType><HospitalId></HospitalId>
		//<ExtUserID>NFYWT</ExtUserID><CardType></CardType><PatientCard></PatientCard><PatientID>33043014</PatientID>
		//<StartDate>2016-09-11</StartDate><EndDate>2016-09-28</EndDate></Request>
		
		//HQTJ 可以查询全部渠道的挂号记录
		//==============封装HIS请求==============
		$TradeCode = "1104";
		$ExtOrgCode ="南方医务通"; 
		$ExtUserID = "HQTJ";  
		$ClientType = ""; 
		$HospitalId = "";    
		$CardType = ""; 
		$PatientCard = "";
		$PatientID = $req->patientId;
		$StartDate = date("Y-m-d",strtotime("-1 month"));
		$EndDate = date("Y-m-d",time()); 
		
		//================调用自己WS===============
		$postData =  "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><CardType>$CardType</CardType><PatientCard>$PatientCard</PatientCard><PatientID>$PatientID</PatientID><StartDate>$StartDate</StartDate><EndDate>$EndDate</EndDate></Request>";		
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryAdmOPReg&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getLastClinicDate:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getLastClinicDate:res\r\n".$res);   
		$res = simplexml_load_string($res);   
		
		//==============处理返回==============    
		
		
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>";
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
		}   
		$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
		
		$xmldata = $res->Orders->Order;  
		//提取相同的分组列表
		$arr = array();
		foreach($xmldata as $key=>$v){   
			if($v->Status=="正常"){
				$arr[] = array("RegId"=>$v->RegID,"AdmitDate"=>$v->AdmitDate);  
			} 
		}  
		$arr = end($arr); 
		if($arr!=null){ 
			$refxml .="<dateTime>".$arr[AdmitDate]."</dateTime><clinicCode>".$arr[RegId]."</clinicCode>";
		}else{
			$refxml .="<dateTime></dateTime><clinicCode></clinicCode>";
		}
		
		$refxml .= "</res>";
		echo $refxml;  	
	}
	
	//<req><hospitalId>1051</hospitalId><patientId>91303204391491</patientId><dateTime>91303204391491</dateTime><clinicCode></clinicCode></req> 
	//检验报告列表查询
	public function labTestListAction(){     
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//==============封装HIS请求==============
		$ClinicSeq = $req->clinicCode;  
		
		//================调用自己WS===============
		$postData = "<Request><ClinicSeq>$ClinicSeq</ClinicSeq></Request>"; 
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=LISgetReport&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("labTestList:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("labTestList:res\r\n".$res);   
		$res = simplexml_load_string($res);
		
		//==============处理返回==============    
		
		//$res = "<Response><ResultCode>0</ResultCode><ResultContent>成功</ResultContent><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005483||B027||1</InspectionId><InspectionName>糖化血红蛋白</InspectionName><InspectionDate>2016-09-21 00:10:48</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>谢传珍</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B002||1</InspectionId><InspectionName>心肌酶谱</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B004||1</InspectionId><InspectionName>电解质三项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B005||1</InspectionId><InspectionName>肝功1</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B008||1</InspectionId><InspectionName>血酯四项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B010||1</InspectionId><InspectionName>血糖测定</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005482||B034||1</InspectionId><InspectionName>肾功五项</InspectionName><InspectionDate>2016-09-21 00:11:30</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>纪婷婷</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005484||C123||1</InspectionId><InspectionName>泌乳素(PRL)</InspectionName><InspectionDate>2016-09-21 00:12:09</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>罗敏</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId>1000005485||A083||1</InspectionId><InspectionName>血细胞分析（五分类）</InspectionName><InspectionDate>2016-09-21 00:10:05</InspectionDate><Status>1</Status><PatientName>曾令清</PatientName><PatientAge>55岁</PatientAge><Gender>女</Gender><DeptName>HQXXGNKMZ-惠侨心血管内科门诊</DeptName><ClinicalDiagnosis>高血压病</ClinicalDiagnosis><ReportDoctorName>刘志伟</ReportDoctorName><ClinicSeq>133</ClinicSeq><InpatientId></InpatientId></Report></Response>";
		
		//$res = "<Response><ResultCode>0</ResultCode><ResultContent>成功</ResultContent><AdmList><Report><InspectionId>800000003087||A083||1</InspectionId><InspectionName>血细胞分析（五分类）</InspectionName><InspectionDate>2016-12-27 00:17:50</InspectionDate><Status>1</Status><PatientName>蔡丽焕</PatientName><PatientAge>24岁</PatientAge><Gender>女</Gender><DeptName>中医内科门诊</DeptName><ClinicalDiagnosis>腰痹病</ClinicalDiagnosis><ReportDoctorName>demo</ReportDoctorName><CheckDoctorName>demo</CheckDoctorName><ClinicSeq>12438</ClinicSeq><InpatientId></InpatientId></Report><Report><InspectionId></InspectionId><InspectionName></InspectionName><InspectionDate></InspectionDate><Status></Status><PatientName></PatientName><PatientAge></PatientAge><Gender></Gender><DeptName></DeptName><ClinicalDiagnosis></ClinicalDiagnosis><ReportDoctorName></ReportDoctorName><CheckDoctorName></CheckDoctorName><ClinicSeq>12438</ClinicSeq><InpatientId></InpatientId></Report></AdmList></Response>";
		
		$refxml = "";  
		$refxml .= "<res>";  
		$xmldata = $res->AdmList->Report;   
		foreach($xmldata as $key=>$v){     
			if($v->InspectionId!=""){ 
				$refxml .="<labTestList><testNO>".$v->InspectionId."</testNO><subject>".$v->InspectionName."</subject><status>".(strval($v->Status) == "1" ? "0":"1")."</status><requestedDateTime>".$v->InspectionDate."</requestedDateTime><resultsRptDateTime>".$v->InspectionDate."</resultsRptDateTime></labTestList>";
			}
		} 
		$refxml .= "</res>";
		echo $refxml;  	   		
	} 
	
	
	
	// <req><hospitalId>1000</hospitalId><testNo>151202646107</testNo></req>  
	//医院检验结果列表
	public function labTestInfoAction(){     
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象  
		
		//==============封装HIS请求==============
		$InspectionId =$req->testNo; 
		
		//================调用自己WS===============
		$postData = "<Request><InspectionId>$InspectionId</InspectionId></Request>"; 
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=LISgetReportItem&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("labTestInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("labTestInfo:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回==============    		
		//$res = "<Response><ResultCode>0</ResultCode><ResultContent>成功</ResultContent><Item><ItemId>AST</ItemId><ItemName>天门冬氨酸氨基转移酶</ItemName><OrderNo>0</OrderNo><TestResult>31</TestResult><Unit>U/L</Unit><ItemRef>15~40</ItemRef><TestDate>2016-09-21 00:11:30</TestDate><ResultFlag>N</ResultFlag><TestEngName>AST</TestEngName><SpecimType>血清</SpecimType></Item>	<Item><ItemId>LDH</ItemId><ItemName>乳酸脱氢酶</ItemName><OrderNo>0</OrderNo><TestResult>151</TestResult><Unit>U/L</Unit><ResultFlag>H</ResultFlag><ItemRef>0~248</ItemRef><TestDate>2016-09-21 00:11:30</TestDate><TestEngName>LDH</TestEngName><SpecimType>血清</SpecimType></Item><Item><ItemId>HBDH</ItemId><ItemName>a-羟丁酸脱氢酶</ItemName><OrderNo>0</OrderNo><TestResult>107</TestResult><Unit>U/L</Unit><ResultFlag>H</ResultFlag><ItemRef>90~180</ItemRef><TestDate>2016-09-21 00:11:30</TestDate><TestEngName>HBDH</TestEngName><SpecimType>血清</SpecimType></Item><Item><ItemId>CK</ItemId><ItemName>肌酸激酶</ItemName><OrderNo>0</OrderNo><TestResult>110</TestResult><Unit>U/L</Unit><ResultFlag>H</ResultFlag><ItemRef>38~174</ItemRef><TestDate>2016-09-21 00:11:30</TestDate><TestEngName>CK</TestEngName><SpecimType>血清</SpecimType></Item><Item><ItemId>CK_MB</ItemId><ItemName>心型肌酸激酶</ItemName><OrderNo>0</OrderNo><TestResult>10</TestResult><Unit>U/L</Unit><ResultFlag>H</ResultFlag><ItemRef>0~24</ItemRef><TestDate>2016-09-21 00:11:30</TestDate><TestEngName>CK_MB</TestEngName><SpecimType>血清</SpecimType></Item></Response>";
		//$res = simplexml_load_string($res);  
		$refxml = "";  
		$refxml .= "<res>";     
		$xmldata = $res->Item;   
		//abnormalIndicator 高低 暂时无  ItemRef范围要拆分
		foreach($xmldata as $key=>$v){      
			$limit =explode('~',$v->ItemRef);
			$abtemp = "";
			if ($v->ResultFlag  == "L")
			{
				$abtemp = "↓";
			}
			else if ($v->ResultFlag  == "H")
			{
				$abtemp = "↑";
			}
			else
			{
				$abtemp = "";
			}
			$refxml .="<labTestInfo><itemNo>".$v->ItemId."</itemNo><reportItemName>".$v->ItemName."</reportItemName><abnormalIndicator>".$abtemp."</abnormalIndicator><result>".$v->TestResult."</result><units>".$v->Unit."</units><lowerLimit>".$limit[0]."</lowerLimit><upperLimit>".$limit[1]."</upperLimit></labTestInfo>";
		} 
		$refxml .= "</res>"; 
		echo $refxml;  	   		
	} 
	
	
	
	//<req><hospitalId>1051</hospitalId><patientId>91303204391491</patientId><dateTime>91303204391491</dateTime><clinicCode></clinicCode></req> 
	//医院检查结果列表
	public function examListAction(){     
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//==============封装HIS请求==============
		$ClinicSeq = $req->clinicCode;  
		
		//================调用自己WS===============
		$postData = "<Request><ClinicSeq>$ClinicSeq</ClinicSeq></Request>";  
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=PACSgetReport&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("examList:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("examList:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回==============    
		
		//$res = "<Response><ResultCode>0</ResultCode><ResultContent>成功</ResultContent><Report><ReportId>13460</ReportId><ReportTitle>腰椎正侧位片</ReportTitle><ReportDate>2016-09-22</ReportDate><Status>1</Status><PatientName>何春</PatientName><PatientAge>1978-04-02</PatientAge><Gender>2</Gender><ClinicalDiagnosis></ClinicalDiagnosis><ClinicSeq>407</ClinicSeq><InpatientId></InpatientId></Report></Response>";
		//$res = simplexml_load_string($res); 
		$refxml = "";  
		$refxml .= "<res>";    
		$xmldata = $res->Report;   
		foreach($xmldata as $key=>$v){      
			$refxml .="<examList><examNo>".$v->ReportId."</examNo><examClass>".$v->ReportTitle."</examClass><status>".(strval($v->Status) == "1" ? "0":"1")."</status><reqDateTime>".$v->ReportDate."</reqDateTime><reportDateTime>".$v->ReportDate."</reportDateTime></examList>";
		} 
		$refxml .= "</res>";
		echo $refxml;  	   		
	} 
	
	
	
	//<req><hospitalId>1000</hospitalId><examNo>151202646107</examNo></req>  
	//医院检验结果列表
	public function examInfoAction(){     
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		//==============封装HIS请求==============
		$ReportId = $req->examNo;   
		
		//================调用自己WS===============
		$postData = "<Request><ReportId>$ReportId</ReportId></Request>"; 
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=PACSgetReportDetail&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("examInfo:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("examInfo:res\r\n".$res);   
		$res = simplexml_load_string($res); 
		
		//==============处理返回==============    
		
		$res = "<Response><ResultCode>0</ResultCode><ResultContent>成功</ResultContent><Item><DeptName>惠侨脊柱骨科门诊</DeptName><ReportDoctorName></ReportDoctorName><CheckParts>()</CheckParts><Examination></Examination><Diagnosis>腰椎生理曲度变直；椎列连续；部分腰椎椎体缘见唇状骨质增生影；第五腰椎横突肥大，左侧与骶骨形成假关节；其余椎体、附件及椎间隙未见异常；软组织未见异常；其它：未见异常。</Diagnosis><CheckDoctorName></CheckDoctorName><ExaminationDate>2016-09-22 16:48:31</ExaminationDate><VerifyDocName></VerifyDocName><VerifyDate>2016-09-22 16:48:31</VerifyDate><AppDocName>张耀旋</AppDocName></Item></Response>";
		$res = simplexml_load_string($res); 
		$refxml = "";  
		$refxml .= "<res>";    
		$xmldata = $res->Item;   
		//abnormal 阳阴性无 recommedation  建议无   examClass  检查项目无
		foreach($xmldata as $key=>$v){       
			$refxml .="<examInfo><description>".$v->Diagnosis."</description><abnormal></abnormal><imperssion>".$v->Examination."</imperssion><recommedation></recommedation><examClass></examClass><reportDateTime>".$v->ExaminationDate."</reportDateTime></examInfo>";
		} 
		$refxml .= "</res>";
		echo $refxml;  	   		
	} 
	
	
	//<req><patientId>91303204391491</patientId><hospitalId>1051</hospitalId></req>  
	//最近一次发票信息
	public function getGuideListAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		
		//HQTJ 可以查询全部渠道的挂号记录
		//==============封装HIS请求==============
		$TradeCode = "1104";
		$TradeCode1 = "90020";
		$ExtOrgCode ="南方医务通"; 
		$ExtUserID = "HQTJ";  
		$ExtUserID1 = "NFYWT";  
		$ClientType = ""; 
		$HospitalId = "";    
		$CardType = ""; 
		$PatientCard = "";
		$PatientID = $req->patientId;
		$StartDate = date("Y-m-d",time());
		$EndDate = date("Y-m-d",time()); 
		
		//================调用自己WS===============
		$postData =  "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><CardType>$CardType</CardType><PatientCard>$PatientCard</PatientCard><PatientID>$PatientID</PatientID><StartDate>$StartDate</StartDate><EndDate>$EndDate</EndDate></Request>";		
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryAdmOPReg&Input=";
		$result = file_get_contents($wsdl.$postData);    
		Log::soaphis("getLastClinicDate:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("getLastClinicDate:res\r\n".$res);   
		$res = simplexml_load_string($res);   
		
		//==============处理返回==============     
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>"; 
			$refxml .="<resultDesc>查询成功</resultDesc>";
			
			
			$xmldata = $res->Orders->Order;  
			//提取相同的分组列表
			$arr = array();
			foreach($xmldata as $key=>$v){   
				if($v->Status=="正常"){
					$arr[] = array("RegId"=>$v->RegID,"AdmitDate"=>$v->AdmitDate);  
				} 
			}  
			$arr = end($arr); 
			if($arr!=null){ 
				//$refxml .="<dateTime>".$arr[AdmitDate]."</dateTime><clinicCode>".$arr[RegId]."</clinicCode>";
				

				$postData =  "<Request><TradeCode>$TradeCode1</TradeCode><Adm>".$arr[RegId]."</Adm><InvoiceNO></InvoiceNO></Request>";
				$postData = str_replace(' ','%20',$postData); 
				$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=GetDirectListByAdm&Input=";
				$result = file_get_contents($wsdl.$postData);    
				Log::soaphis("GetDirectListByAdm:req\r\n".$postData);     
				$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
				Log::soaphis("GetDirectListByAdm:res\r\n".$res);   
				$res = simplexml_load_string($res);  
				
				if($res->resultCode=="0") {  
					$refxml .= $res->invoiceList->asXML();
				}else{ 
					$refxml .="<resultCode>1</resultCode>";
					$refxml .="<resultDesc>".$res->errorMsg."</resultDesc>";
				}
				
			}

			
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
			$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
		}   
		
		/* $refxml = "";  
		$refxml .= "<res>";   
		$res ="<Response><resultCode>0</resultCode><errorMsg>成功</errorMsg><invoiceList><invoice><head><patientID>ZA00001</patientID><patientName>zhangd</patientName><sex>男</sex><age>23</age><admReason>自费</admReason><cost>1000</cost><doctorName>doctorLi</doctorName><diagnose>感冒</diagnose><guser>张溜</guser><payTime>2016-09-09 09:09:09</payTime></head><body><laboratory><specimenList><specimen><specimenDesc>血清</specimenDesc><guide>请您到门诊二楼采血室采血</guide><prompt>不用禁食</prompt></specimen><specimen><specimenDesc>血清333</specimenDesc><guide>请您到门诊二楼采血室采血3333333</guide><prompt>不用禁食3333333</prompt></specimen></specimenList></laboratory><examination><examList><examItem><itemName>肝胆，脾彩超检查----111</itemName><amt>200----111</amt><date>2016-09-09----111</date><ordDept>外科门诊----111</ordDept><guide>请您到外科门诊B超室做检查----111</guide><depLocPosition>门诊四楼B超室----111</depLocPosition><bookedNote>去除膏药等体外异物。----111</bookedNote></examItem><examItem><itemName>肝胆，脾彩超检查----222</itemName><amt>200----222</amt><date>2016-09-09----222</date><ordDept>外科门诊----222</ordDept><guide>请您到外科门诊B超室做检查----222</guide><depLocPosition>门诊四楼B超室----222</depLocPosition><bookedNote>去除膏药等体外异物。----222</bookedNote></examItem><examItem><itemName>肝胆，脾彩超检查----333</itemName><amt>200----333</amt><date>2016-09-09----333</date><ordDept>外科门诊----333</ordDept><guide>请您到外科门诊B超室做检查----333</guide><depLocPosition>门诊四楼B超室----333</depLocPosition><bookedNote>去除膏药等体外异物。----333</bookedNote></examItem></examList></examination><treatment><treatDeptList><treatDept><deptName>皮肤科门诊--11</deptName><guide>请您到门诊四楼皮肤科门诊区 皮肤科门诊---11</guide><treatItemList><treatItem><itemName>光动力治疗---333</itemName><qty>4</qty><uom>次</uom><amt>23</amt></treatItem></treatItemList></treatDept><treatDept><deptName>皮肤科门诊--22</deptName><guide>请您到门诊四楼皮肤科门诊区 皮肤科门诊---22</guide><treatItemList><treatItem><itemName>光动力治疗---333</itemName><qty>4</qty><uom>次</uom><amt>23</amt></treatItem></treatItemList></treatDept></treatDeptList></treatment><drug><baseDrug><baseDrugDeptList><baseDrugDept><deptName>磁共振室--1</deptName><guide>请您到第一医技楼磁共振室--1</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--2</deptName><guide>请您到第一医技楼磁共振室--2</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--3</deptName><guide>请您到第一医技楼磁共振室--3</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--4</deptName><guide>请您到第一医技楼磁共振室--4</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept></baseDrugDeptList></baseDrug><druglist><drugItem><drugType>西药、中成药</drugType><guide>请您到门诊一楼门诊药房</guide><window>5号窗口</window><prompt>请先到自助机报到后，再等待取药</prompt></drugItem><drugItem><drugType>中草药</drugType><guide>请您到门诊一楼草药房</guide><window>7号窗口</window><prompt>请先到自助机报到后，再等待取药</prompt></drugItem></druglist></drug></body></invoice><invoice><head><patientID>ZA00001</patientID><patientName>zhangd</patientName><sex>男</sex><age>23</age><admReason>自费</admReason><cost>1000</cost><doctorName>doctorLi</doctorName><diagnose>感冒</diagnose><guser>张溜</guser><payTime>2016-09-09 09:09:09</payTime></head><body><laboratory><specimenList><specimen><specimenDesc>血清</specimenDesc><guide>请您到门诊二楼采血室采血</guide><prompt>不用禁食</prompt></specimen><specimen><specimenDesc>血清333</specimenDesc><guide>请您到门诊二楼采血室采血3333333</guide><prompt>不用禁食3333333</prompt></specimen></specimenList></laboratory><examination><examList><examItem><itemName>肝胆，脾彩超检查----111</itemName><amt>200----111</amt><date>2016-09-09----111</date><ordDept>外科门诊----111</ordDept><guide>请您到外科门诊B超室做检查----111</guide><depLocPosition>门诊四楼B超室----111</depLocPosition><bookedNote>去除膏药等体外异物。----111</bookedNote></examItem><examItem><itemName>肝胆，脾彩超检查----222</itemName><amt>200----222</amt><date>2016-09-09----222</date><ordDept>外科门诊----222</ordDept><guide>请您到外科门诊B超室做检查----222</guide><depLocPosition>门诊四楼B超室----222</depLocPosition><bookedNote>去除膏药等体外异物。----222</bookedNote></examItem><examItem><itemName>肝胆，脾彩超检查----333</itemName><amt>200----333</amt><date>2016-09-09----333</date><ordDept>外科门诊----333</ordDept><guide>请您到外科门诊B超室做检查----333</guide><depLocPosition>门诊四楼B超室----333</depLocPosition><bookedNote>去除膏药等体外异物。----333</bookedNote></examItem></examList></examination><treatment><treatDeptList><treatDept><deptName>皮肤科门诊--11</deptName><guide>请您到门诊四楼皮肤科门诊区 皮肤科门诊---11</guide><treatItemList><treatItem><itemName>光动力治疗---333</itemName><qty>4</qty><uom>次</uom><amt>23</amt></treatItem></treatItemList></treatDept><treatDept><deptName>皮肤科门诊--22</deptName><guide>请您到门诊四楼皮肤科门诊区 皮肤科门诊---22</guide><treatItemList><treatItem><itemName>光动力治疗---333</itemName><qty>4</qty><uom>次</uom><amt>23</amt></treatItem></treatItemList></treatDept></treatDeptList></treatment><drug><baseDrug><baseDrugDeptList><baseDrugDept><deptName>磁共振室--1</deptName><guide>请您到第一医技楼磁共振室--1</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--2</deptName><guide>请您到第一医技楼磁共振室--2</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--3</deptName><guide>请您到第一医技楼磁共振室--3</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept><baseDrugDept><deptName>磁共振室--4</deptName><guide>请您到第一医技楼磁共振室--4</guide><baseDrugItemList><baseDrugItem><itemName>注射液--11</itemName><qty>2</qty><uom>支</uom><amt>11</amt></baseDrugItem><baseDrugItem><itemName>注射液--22</itemName><qty>3</qty><uom>支</uom><amt>12</amt></baseDrugItem><baseDrugItem><itemName>注射液--33</itemName><qty>4</qty><uom>支</uom><amt>13</amt></baseDrugItem></baseDrugItemList></baseDrugDept></baseDrugDeptList></baseDrug><druglist><drugItem><drugType>西药、中成药</drugType><guide>请您到门诊一楼门诊药房</guide><window>5号窗口</window><prompt>请先到自助机报到后，再等待取药</prompt></drugItem><drugItem><drugType>中草药</drugType><guide>请您到门诊一楼草药房</guide><window>7号窗口</window><prompt>请先到自助机报到后，再等待取药</prompt></drugItem></druglist></drug></body></invoice></invoiceList></Response>";
		$res = simplexml_load_string($res);   
		if($res->resultCode=="0") {  
		$refxml .="<resultCode>0</resultCode>";
		$refxml .="<resultDesc>查询成功</resultDesc>";   
		$refxml .= $res->invoiceList->asXML();
		}else{ 
		$refxml .="<resultCode>1</resultCode>";
		$refxml .="<resultDesc>".$res->errorMsg."</resultDesc>";
		}*/
		
		$refxml .= "</res>";
		echo $refxml;  	
	}
	
	
	//<req><patientId>91303204391491</patientId><hospitalId>1051</hospitalId></req>  
	//排队候诊(4001)
	public function waitingQueueAction(){    
		//==============获得前台请求==============  
		$input = file_get_contents("php://input"); //接收POST数据		
		$req = simplexml_load_string($input); //提取POST数据为simplexml对象 
		
		
		//HQTJ 可以查询全部渠道的挂号记录
		//==============封装HIS请求==============
		$TradeCode = "1104";
		$TradeCode1 = "4001";
		$ExtOrgCode ="南方医务通"; 
		$ExtUserID = "HQTJ";  
		$ExtUserID1 = "NFYWT";
		$ClientType = ""; 
		$HospitalId = "";    
		$CardType = ""; 
		$PatientCard = "";
		$PatientID = $req->patientId;
		$StartDate = date("Y-m-d",time());
		$EndDate = date("Y-m-d",time()); 
		
		//================调用自己WS===============
		$postData =  "<Request><TradeCode>$TradeCode</TradeCode><ExtOrgCode>$ExtOrgCode</ExtOrgCode><ClientType>$ClientType</ClientType><HospitalId>$HospitalId</HospitalId><ExtUserID>$ExtUserID</ExtUserID><CardType>$CardType</CardType><PatientCard>$PatientCard</PatientCard><PatientID>$PatientID</PatientID><StartDate>$StartDate</StartDate><EndDate>$EndDate</EndDate></Request>";		
		$postData = str_replace(' ','%20',$postData); 
		$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=QueryAdmOPReg&Input=";
		$result = file_get_contents($wsdl.$postData);  
		//$result = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData); 
		Log::soaphis("QueryAdmOPRe:req\r\n".$postData);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryAdmOPRe:res\r\n".$res);   
		$res = simplexml_load_string($res);   
		
		
		
		//==============处理返回==============     
		$refxml = "";  
		$refxml .= "<res>";   
		if($res->ResultCode=="0") { 
			$refxml .="<resultCode>0</resultCode>"; 
			$refxml .="<resultDesc>查询成功</resultDesc>";
			
			
			$xmldata = $res->Orders->Order;  
			//提取相同的分组列表
			$arr = array();
			foreach($xmldata as $key=>$v){   
				if($v->Status=="正常"){
					$arr[] = array("RegId"=>$v->RegID,"AdmitDate"=>$v->AdmitDate);  
				} 
			}  
			$arr = end($arr); 
			if($arr!=null){ 
				$refxml .="<dateTime>".$arr[AdmitDate]."</dateTime><clinicCode>".$arr[RegId]."</clinicCode>";
				
				
				$postData =  "<Request><TradeCode>$TradeCode1</TradeCode><AdmNo>".$arr[RegId]."</AdmNo><ExtUserID>$ExtUserID1</ExtUserID></Request>";
				$postData = str_replace(' ','%20',$postData); 
				$wsdl = "http://nfyyhis.test.ywtinfo.com/index.php?soap_method=WaitingQueue&Input=";
				$result = file_get_contents($wsdl.$postData);    
				Log::soaphis("WaitingQueue:req\r\n".$postData);     
				$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
				Log::soaphis("WaitingQueue:res\r\n".$res);   
				$res = simplexml_load_string($res);  
				
				if($res->ResultCode=="0") { 
					
					$refxml .="<patName>".$v->PatName."</patName><admLoc>".$v->AdmLoc."</admLoc><admDoc>".$v->AdmDoc."</admDoc><waitingNumber>".$v->WaitingNumber."</waitingNumber>";
					
				}else{ 
					$refxml .="<resultCode>1</resultCode>";
					$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
				}
				
			}

			
		}
		else{
			$refxml .="<resultCode>1</resultCode>";
			$refxml .="<resultDesc>".$res->ResultContent."</resultDesc>";
		}   
		
		$refxml .= "</res>";
		echo $refxml;  
		
		/*$refxml = "";  
		$refxml .= "<res>";  
		$refxml .="<resultCode>0</resultCode>"; 
		$refxml .="<resultDesc>查询成功</resultDesc>";
		$refxml .="<dateTime>2017-01-04</dateTime><clinicCode>20</clinicCode><patName>测试姓名</patName><admLoc>就诊科室</admLoc><admDoc>就诊医生</admDoc><waitingNumber>12</waitingNumber>";
		$refxml .= "</res>";
		echo $refxml;  	*/
	}
	
	
	
}

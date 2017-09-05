<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');    

class ApiService extends  BaseService{
	
	//查询二级科室列表(1012) 
	public function QueryDepartment($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh1.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryDepartment&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryDepartment:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryDepartment:res\r\n".$res,$appUser);  
		echo $res;  
	}
	
	//查询医生列表(1013)
	public function QueryDoctor($appUser){   
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh1.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryDoctor&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryDoctor:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryDoctor:res\r\n".$res,$appUser);  
		echo $res;   
	}
	
	
	//查询排班记录(1004)
	public function QuerySchedule($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh1.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QuerySchedule&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QuerySchedule:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QuerySchedule:res\r\n".$res,$appUser);  
		echo $res;  
	}
	
	 
	//查询排班记录(10041)
	public function QueryScheduleTimeInfo($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh1.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryScheduleTimeInfo&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryScheduleTimeInfo:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryScheduleTimeInfo:res\r\n".$res,$appUser);  
		echo $res;    
	}
	
	//查询排班记录(10041)
	public function QueryScheduleToHQTJ($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh1.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryScheduleToHQTJ&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryScheduleToHQTJ:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryScheduleToHQTJ:res\r\n".$res,$appUser);  
		echo $res;    
	}
	 
	
	//查询停诊医生信息 (1107)
	public function QueryStopDoctorInfo($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryStopDoctorInfo&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryStopDoctorInfo:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryStopDoctorInfo:res\r\n".$res,$appUser);  
		echo $res;    
	}
	
	
	//锁号(10015)
	public function LockOrder($appUser){      
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=LockOrder&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("LockOrder:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("LockOrder:res\r\n".$res,$appUser);  
		echo $res;    
	}
	
	
	
	//取消锁号(10016)
	public function UnLockOrder($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=UnLockOrder&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("UnLockOrder:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("UnLockOrder:res\r\n".$res,$appUser);  
		echo $res;    
	}
	


	//预约（1000）
	public function BookService($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=BookService&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("BookService:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("BookService:res\r\n".$res,$appUser);  
		echo $res;     
	}
	
	//取消预约（1108）
	public function CancelOrder($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=CancelOrder&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("CancelOrder:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("CancelOrder:res\r\n".$res,$appUser);  
		echo $res;     
	}
	
	//病人取号确认（2001）
	public function OPAppArrive($appUser){ 
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=OPAppArrive&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("OPAppArrive:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("OPAppArrive:res\r\n".$res,$appUser);  
		echo $res;    
	}
	
	
	//挂号-提前挂号(1101)
	public function OPRegister($appUser){  
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=OPRegister&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("OPRegister:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("OPRegister:res\r\n".$res,$appUser);  
		echo $res;     
	}
	
	
	//退号（1003）
	public function CancelReg($appUser){   
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=CancelReg&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("CancelReg:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("CancelReg:res\r\n".$res,$appUser);  
		echo $res;      
	} 
	
	
	
	//查询挂号记录(1104)
	public function QueryAdmOPReg($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryAdmOPReg&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryAdmOPReg:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryAdmOPReg:res\r\n".$res,$appUser);  
		echo $res;       
	} 
	
	
	//查询预约记录（1005）
	public function QueryOrder($appUser){ 
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=QueryOrder&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("QueryOrder:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("QueryOrder:res\r\n".$res,$appUser);  
		echo $res;      
	} 
	
	
	
	
	//获取患者基本信息(3300)
	public function GetPatInfo($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=GetPatInfo&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("GetPatInfo:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("GetPatInfo:res\r\n".$res,$appUser);  
		echo $res;      
	}
	
	
	
	//新建病人主索引（3014，3011）
	public function SavePatientCard($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=SavePatientCard&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("SavePatientCard:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("SavePatientCard:res\r\n".$res,$appUser);  
		echo $res;       
	}
	
	
	//排队候诊(4001)
	public function WaitingQueue($appUser){      
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=WaitingQueue&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("WaitingQueue:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("WaitingQueue:res\r\n".$res,$appUser);  
		echo $res;        
	} 
	
	
	
	//检验报告列表查询
	public function LISgetReport($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=LISgetReport&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("LISgetReport:req\r\n".$req,$appUser);     
		Log::soaphis("LISgetReport:res\r\n".$result,$appUser);  
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("LISgetReport:res\r\n".$res,$appUser);  
		echo $res;         
	} 
	
	
	//检验报告明细内容查询
	public function LISgetReportItem($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=LISgetReportItem&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("LISgetReportItem:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("LISgetReportItem:res\r\n".$res,$appUser);  
		echo $res;           
	}
	
	
	//检查报告列表查询接口
	public function PACSgetReport($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=PACSgetReport&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("PACSgetReport:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("PACSgetReport:res\r\n".$res,$appUser);  
		echo $res;            
	} 
	
	
	//检查报告明细内容查询接口
	public function PACSgetReportDetail($appUser){    
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=PACSgetReportDetail&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("PACSgetReportDetail:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("PACSgetReportDetail:res\r\n".$res,$appUser);  
		echo $res;              
	}
	
	//查询就诊信息(4002）
	public function AdmInfo($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=AdmInfo&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("AdmInfo:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("AdmInfo:res\r\n".$res,$appUser);  
		echo $res;               
	}
	
	
	//处方单查询（3233）
	public function GetPresc($appUser){     
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=GetPresc&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("GetPresc:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("GetPresc:res\r\n".$res,$appUser);  
		echo $res;                
	}
	
	
	//第三方对账查询(5001)
	public function FindWeChatPayData($appUser){   
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=FindWeChatPayData&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("FindWeChatPayData:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("FindWeChatPayData:res\r\n".$res,$appUser);  
		echo $res;   
	} 
	
	
	//HIS对账查询(5002)
	public function AccountingConfirm($appUser){
		$req = file_get_contents("php://input"); //接收POST数据 
		$wsdl = "http://yygh2.dept.nfyy.com/csp/oep/DHC.OEP.BS.OEPSTANWebService.cls?soap_method=AccountingConfirm&Input=";
		$result = file_get_contents($wsdl.$req);   
		
		Log::soaphis("AccountingConfirm:req\r\n".$req,$appUser);     
		$res = call_user_func(array($_ENV["commonClass"],"SoapToXml"),$result);
		Log::soaphis("AccountingConfirm:res\r\n".$res,$appUser);  
		echo $res;    
	} 
	 
}

?>

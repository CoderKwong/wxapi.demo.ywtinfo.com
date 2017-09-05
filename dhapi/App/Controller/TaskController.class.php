<?php
error_reporting(0); 

if (!defined('IS_INITPHP')) exit('Access Denied!');    



class TaskController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	
	//下发Sms
	public function goSMSAction(){   
		$key =I('key');
		
		if($key=="gosms")
		{   
			//$url='http://121.14.17.208/Server/SMS_Send.aspx?sn=SDK-NFY-020-00011&pwd=NFYYYYGHAPP&mobile=18011770183&content=%e4%bd%a0%e5%a5%bd&ext=1';  
			//$html = file_get_contents($url);  
			//echo $html;  
		}
		
	} 
	
	//下发DDPUSH
	public function goDDPushAction(){   
		$key =I('key');
		if($key=="goddpushtask")
		{ 
			includeIfExist(C('APP_FULL_PATH').'/Lib/DDPusher.class.php'); 
			try { 
				$serverIp =C('PushServerIp');
				$serverPort =C('PushServerPort');
				$appId =C('PushAppId'); 
				$version =C('PushVersion'); 
				
				$ddpush = new DDpusher($serverIp,$serverPort);
				$sqlString = "SELECT * FROM hz_pushtask p,hz_customeruser c WHERE p.customerUserId=c.id AND c.timestamp+180000>UNIX_TIMESTAMP() AND c.uuid<>'' AND devType='android'  AND p.pushStatus='run' AND p.pushTime>=DATE_SUB(SYSDATE(),INTERVAL 0 MINUTE) AND p.pushTime<=DATE_ADD(SYSDATE(),INTERVAL 1 MINUTE)";
				//$sqlString = "SELECT p.*,c.uuid FROM hz_pushtask p,hz_customeruser c WHERE p.customerUserId=c.id AND c.timestamp+1800000>UNIX_TIMESTAMP()  AND p.pushStatus='run'";
				$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");   
				foreach($sqlData as $key=>$v){   
					//规则 uid※keyid※content※url※parameter※type※pushTime
					//规则 uid※keyid※contente※pushTime※eventType※parameter
					$data = $v["customerUserId"]."※".$v["id"]."※".$v["content"]."※".$v["pushTime"]."※".$v["eventType"]."※".$v["parameter"];  
					$ddpush->push0x20($v["uuid"],$data,$version,$appId);  
				}
			} catch (Exception $ex) {
				echo $ex->getMessage();
			}  
			echo '下发成功';   
		}
	}
	
	//同步HIS号源，删除今天之前的号源记录
	//生成用药计划
	public function goHospitalAppointsAction(){   
		$key =I('key');
		if($key=="gohospitalappointstask")
		{
			$hospitalId =C('HOSPITALID');
			$hisUrl =C('HIS_URL');
			$maxRegDays =C('maxRegDays'); 
			$appKey= md5(C('APPKEY')."|".date("Y-m-d", time()));  
			//$this->getHospitalInfo($hospitalId,$hisUrl,$appKey);
			$this->getDeptInfo($hospitalId,$hisUrl,$appKey);
			$this->getDoctorInfo($hospitalId,$hisUrl,$appKey);
			//$this->getRegInfo($hospitalId,$hisUrl,$appKey,$maxRegDays);
			 
			//删除今天前的数据 
			//$this->delRegList(); 
		}
	}
	
	//同步HIS号源，删除今天之前的号源记录
	//生成用药计划
	public function goAllRegInfoAction(){   
		$key =I('key');
		if($key=="goallreginfotask")
		{
			$hospitalId =C('HOSPITALID');
			$hisUrl =C('HIS_URL');
			$maxRegDays =C('maxRegDays'); 
			$appKey= md5(C('APPKEY')."|".date("Y-m-d", time()));   
			$this->getRegInfo($hospitalId,$hisUrl,$appKey,$maxRegDays); 
		}
	}
	
	//生成用药计划
	public function goMedicineAction(){   
		$key =I('key');
		if($key=="gomedicinetask")
		{
			$this->codeMedicine(); 
			
		}
	}
	
	//删除短信列表
	public function goVerifySmsCodeAction(){   
		$key =I('key');
		if($key=="goverifysmscodetask")
		{
			$this->delVerifySmsCode();
		}
	}
	
	public function goFavoriteNumAction(){   
		$key =I('key');
		if($key=="gofavoritenumtask")
		{
			$this->getFavoriteNum();  
		}
	}
	
	
	//同步HIS号源，删除今天之前的号源记录
	//生成用药计划
	public function goSynRegInfoAction(){   
		$key =I('key');
		if($key=="gosynreginfotask")
		{
			$hospitalId =C('HOSPITALID');
			$hisUrl =C('HIS_URL');
			$appKey= md5(C('APPKEY')."|".date("Y-m-d", time()));  
			$this->getRegSynInfo($hospitalId,$hisUrl,$appKey);		
		}
	}
	
	//删除号源表今天前的数据
	public function delRegList() {   
		$sqlString ="DELETE FROM hz_reginfo 	WHERE regDate<DATE('".date("Y-m-d", time())."')";
		$sqlData = call_user_func(array($_ENV["dbDao"],"delete"),$sqlString,"entity");  
	}
	
	//删除短信验证码今天前的数据
	public function delVerifySmsCode() {   
		$sqlString ="DELETE FROM hz_verifysmscode 	WHERE DATE(createtime)<DATE('".date("Y-m-d", time())."')";
		$sqlData = call_user_func(array($_ENV["dbDao"],"delete"),$sqlString,"entity");  
	}
	
	//医院信息查询接口
	public function getHospitalInfo($hospitalId,$hisUrl,$appKey) {  
		$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId></req>"; 
		$postUrl = $hisUrl."doReqToHis.aspx?service=getHospitalInfo&appkey=".$appKey;  
		$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData); 
		
		$sqlString ="select id from hz_hospitalinfo where hospitalId='$hospitalId'";
		$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
		if ($sqlData) {
			$sqlString ="update hz_hospitalinfo set hospitalName='".trim(strval($xmldata->hospitalName))."',addr='".trim(strval($xmldata->addr))."',tel='".trim(strval($xmldata->tel))."',webSite='".trim(strval($xmldata->webSite))."',hospitalLevel='".trim(strval($xmldata->hospLevel))."',hospitalArea='".trim(strval($xmldata->hospArea))."',info='".trim(strval($xmldata->desc))."',maxRegDays='".trim(strval($xmldata->maxRegDays))."',startRegTime='".trim(strval($xmldata->startRegTime))."',stopRegTime='".trim(strval($xmldata->stopRegTime))."',stopBookTimeM='".trim(strval($xmldata->stopBookTimeM))."',stopBookTimeA='".trim(strval($xmldata->stopBookTimeA))."' where hospitalId='$hospitalId'";
			call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
		}else{
			$sqlString ="insert into hz_hospitalinfo(hospitalId,hospitalName,addr,tel,webSite,hospitalLevel,hospitalArea,info,maxRegDays,startRegTime,stopRegTime,stopBookTimeM,stopBookTimeA,createDate) VALUES('".$hospitalId."','".trim(strval($xmldata->hospitalName))."','".trim(strval($xmldata->addr))."','".trim(strval($xmldata->tel))."','".trim(strval($xmldata->webSite))."','".trim(strval($xmldata->hospLevel))."','".trim(strval($xmldata->hospArea))."','".trim(strval($xmldata->desc))."','".trim(strval($xmldata->maxRegDays))."','".trim(strval($xmldata->startRegTime))."','".trim(strval($xmldata->stopRegTime))."','".trim(strval($xmldata->stopBookTimeM))."','".trim(strval($xmldata->stopBookTimeA))."','".date("Y-m-d H:i:s", time())."')";
			call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");  
		}  
	}
	
	//科室信息查询接口
	public function getDeptInfo($hospitalId,$hisUrl,$appKey)  { 
		$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId></deptId></req>";  
		$postUrl = $hisUrl."?c=NfyyApi&a=getDeptInfo&appkey=".$appKey;   
		$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);
		 
		foreach($xmldata as $key=>$value){   
			$sqlString ="select id from hz_deptinfo where hospitalId='$hospitalId' and deptId ='$value->deptId'";
			$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
			
			if ($sqlData) {  
				$sqlString ="update hz_deptinfo set deptId='$value->deptId',deptName='$value->deptName',parentId='$value->parentId',visitAddress='$value->visitAddress',info='$value->desc' where id='".$sqlData['id']."'";
				call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
			}else{   
				$sqlString ="insert into hz_deptinfo(hospitalId,deptId,deptName,parentId,visitAddress,info) VALUES('$hospitalId','$value->deptId','$value->deptName','$value->parentId','$value->visitAddress','$value->desc')";
				call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");  
			}  
		}      
	}   

	//医生信息查询接口
	public function getDoctorInfo($hospitalId,$hisUrl,$appKey){ 
		$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId/><doctorId/></req>"; 
		$postUrl = $hisUrl."?c=NfyyApi&a=getDoctorInfo&appkey=".$appKey; 
		$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);
		
		foreach($xmldata as $key=>$value){  
			$sqlString ="select id from hz_doctorinfo where hospitalId='$hospitalId' and deptId ='$value->deptId' and doctorId ='$value->doctorId'";
			$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
			if ($sqlData) {  
				$sqlString ="update hz_doctorinfo set deptId='$value->deptId',doctorId='$value->doctorId',doctorName='$value->doctorName',title='$value->Title',fee='$value->Fe',sex='".trim($value->Gender == "M" ? "男":"女")."',info='$value->desc' where id='".$sqlData['id']."'";
				call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
			}else{  
				$sqlString ="insert into hz_doctorinfo(hospitalId,deptId,doctorId,doctorName,title,fee,sex,info) VALUES('$hospitalId','$value->deptId','$value->doctorId','$value->doctorName','$value->Title','$value->Fee','".trim($value->Gender == "M" ? "男":"女")."','$value->desc')";
				call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");  
			}    
		}     
	}   

	//医生号源信息查询接口
	public function getRegInfo($hospitalId,$hisUrl,$appKey,$maxRegDays){  
		$sqlString ="select id,deptId from hz_deptinfo where hospitalId='$hospitalId' and parentId <>'-1'";
		$sqlDataList = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");   
		
		foreach($sqlDataList as $key=>$deptInfo){  
			
			$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId>".$deptInfo['deptId']."</deptId><doctorId></doctorId><startDate>".date("Y-m-d",strtotime("+6 day"))."</startDate><endDate>".date("Y-m-d", strtotime("+".$maxRegDays." day"))."</endDate></req>"; 
			$postUrl = $hisUrl."?c=NfyyApi&a=getRegInfo&appkey=".$appKey;     
			$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);
			
			$xmldata = $xmldata->Schedules->Schedule; 
			 
			foreach($xmldata as $key=>$v){  
				 
				$sqlString ="select id from hz_reginfo where hospitalId='$hospitalId' and deptId='".$deptInfo['deptId']."' and doctorId='$v->DoctorCode' and regDate='$v->ServiceDate' and timeFlag ='$v->SessionCode'";
				$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
				if ($sqlData) {  
					//strval($v->TimeRangeFlag=="Y" ? "1": "0")
					$sqlString ="update hz_reginfo set deptId='".$deptInfo['deptId']."',doctorId='$v->DoctorCode',doctorName='$v->DoctorName',regDate='$v->ServiceDate',regWeekDay='$v->WeekDay',timeFlag='$v->SessionCode',regStatus='$v->ScheduleStatus',regTotalCount='$v->AvailableTotalNum',regLeaveCount='$v->AvailableLeftNum',regFee='(float)(strval($v->RegFee))*100',treatFee='(float)(strval($v->CheckupFee))*100',isTimeReg='$v->TimeRangeFlag' where id='".$sqlData['id']."'";
					call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
				}else{  
					$sqlString ="insert into hz_reginfo(hospitalId,deptId,doctorId,doctorName,regDate,regWeekDay,timeFlag,regStatus,regTotalCount,regleaveCount,regFee,treatFee,isTimeReg) VALUES('$hospitalId','".$deptInfo['deptId']."','$v->DoctorCode','$v->DoctorName','$v->ServiceDate','$v->WeekDay','$v->SessionCode','$v->ScheduleStatus','$v->AvailableTotalNum','$v->AvailableLeftNum','$v->RegFee','$v->CheckupFee','$v->TimeRangeFlag')";
					call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");  
				}   
			} 
		}   
	}  
	
	
	//生成药接口
	public function codeMedicine(){   
		
		$sqlString ="SELECT * FROM  hz_medicineitemschedulegroup g WHERE  DATE_ADD(g.startDate, INTERVAL g.days-1 DAY)>= DATE('".date("Y-m-d", time())."')";
		$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");    
		foreach($sqlData as $key=>$misg){   
			$sqlString ="SELECT * FROM  hz_medicineschedule g WHERE medicineItemId='".$misg['medicineItemId']."' and scheduleGroupId='".$misg['id']."' and  DATE(g.originalDateTime)= DATE('".date("Y-m-d", time())."');";
			$sqlData1 = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity"); 
			$sqlString ="SELECT * FROM  hz_medicineitem g WHERE id='".$misg['medicineItemId']."'";
			$sqlData2 = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");  
			$sqlString ="SELECT * FROM  hz_medicine g WHERE id='".$sqlData2['medicineId']."'";
			$sqlData3 = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity"); 
			if (!$sqlData1) {  
				$sqlString ="UPDATE hz_medicineitemschedulegroup SET dayConsumption = dayConsumption+1,daysToTake = daysToTake+1 WHERE id ='".$misg['id']."'"; 
				$sqlDatau = call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
				$consumptionHoursString = explode(',', $misg['consumptionHoursString']); 
				$quantityString = explode(',',$misg['quantityString']); 
				$startTime = date("Y-m-d", time());
				$doseType =  $misg['doseType'];
				$drugName =  $sqlData2['drugName'];
				$customerUserId =$sqlData3['customerUserId'];
				$customerFamilyId =$sqlData3['customerFamilyId'];
				
				$data_values=null;
				$data_values1=null;
				for ($x=0; $x<=count($consumptionHoursString)-1; $x++) {
					$data_values .= "('".$misg['id']."','".$startTime." ".$consumptionHoursString[$x]."','".$startTime." ".$consumptionHoursString[$x]."','pending','".$misg['medicineItemId']."','".$consumptionHoursString[$x]."','".$quantityString[$x]."','".$doseType."'),"; 
					$data_values1 .="('".$misg['id']."','服用','".$drugName.$quantityString[$x].$doseType."','".$customerUserId."','".$customerFamilyId."','2','".$startTime." ".$consumptionHoursString[$x].":00','".date("Y-m-d H:i:s", time())."'),"; 
				}   
				$data_values = substr($data_values,0,-1); //去掉最后一个逗号  
				$sqlString= "INSERT INTO hz_medicineschedule (scheduleGroupId,actualDateTime,originalDateTime,status,medicineItemId,consumptionHours,quantity,doseType) VALUES". $data_values;
				call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");   
				
				$data_values1 = substr($data_values1,0,-1); //去掉最后一个逗号  
				$sqlString1= "INSERT INTO hz_pushtask (parameter,title,content,customerUserId,customerFamilyId,eventType,pushTime,createTime)	VALUES" . $data_values1;
				call_user_func(array($_ENV["dbDao"],"insert"),$sqlString1,"return");    
			}  
		}  
	} 
	
	
	//医生号源信息查询接口 5分钟活跃同步
	public function getSynRegInfo($hospitalId,$hisUrl,$appKey){  
		$sqlString ="SELECT deptId,doctorId,regDate FROM  hz_appointsorder t WHERE hospitalId='$hospitalId' and resultCode=1  resultCode=1 AND orderTime>=DATE_SUB(SYSDATE(),INTERVAL 5 MINUTE) AND orderTime<=DATE_ADD(SYSDATE(),INTERVAL 0 MINUTE)  GROUP BY  deptId,doctorId,regDate  ";
		$sqlDataList = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");   
		
		foreach($sqlDataList as $key=>$deptInfo){  
			
			$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId>".$deptInfo['deptId']."</deptId><doctorId>".$deptInfo['doctorId']."</doctorId><startDate>".$deptInfo['regDate']."</startDate><endDate>".$deptInfo['regDate']."</endDate></req>"; 
			$postUrl = $hisUrl."doReqToHis.aspx?service=getRegInfo&appkey=".$appKey;     
			$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);
			
			foreach($xmldata as $key=>$ri){  
				
				$TimeRegInfoList = $ri->TimeRegInfoList;
				foreach($TimeRegInfoList as $key=>$tril){  
					
					$timeRegInfo = $tril->timeRegInfo;    
					foreach($timeRegInfo as $key=>$tri){     
						$sqlString ="select id from hz_reginfo where hospitalId='$hospitalId' and deptId='".$deptInfo['deptId']."' and doctorId='$ri->doctorId' and regDate='$tril->regDate' and timeFlag ='$tri->timeFlag'";
						$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
						if ($sqlData) {  
							$sqlString ="update hz_reginfo set deptId='".$deptInfo['deptId']."',doctorId='$ri->doctorId',doctorName='$ri->doctorName',regDate='$tril->regDate',regWeekDay='$tril->regWeekDay',timeFlag='$tri->timeFlag',regStatus='$tri->regStatus',regTotalCount='$tri->regTotalCount',regLeaveCount='$tri->regleaveCount',regFee='$tri->regFee',treatFee='$tri->treatFee',isTimeReg='$tri->isTimeReg' where id='".$sqlData['id']."'";
							call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
						}else{  
							$sqlString ="insert into hz_reginfo(hospitalId,deptId,doctorId,doctorName,regDate,regWeekDay,timeFlag,regStatus,regTotalCount,regleaveCount,regFee,treatFee,isTimeReg) VALUES('$hospitalId','".$deptInfo['deptId']."','$ri->doctorId','$ri->doctorName','$tril->regDate','$tril->regWeekDay','$tri->timeFlag','$tri->regStatus','$tri->regTotalCount','$tri->regleaveCount','$tri->regFee','$tri->treatFee','$tri->isTimeReg')";
							call_user_func(array($_ENV["dbDao"],"insert"),$sqlString,"return");  
						}   
					}
				} 
			} 
		}   
	}  
	
	
	
	//医生号源信息查询接口 60分钟活跃同步
	public function getFavoriteNum($hospitalId,$hisUrl,$appKey){  
		$sqlString ="SELECT COUNT(1) as rows,hospitalId,deptId,doctorId FROM hz_favoritedoctor WHERE STATUS=1 GROUP BY hospitalId,deptId,doctorId ";
		$sqlDataList = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");    
		foreach($sqlDataList as $key=>$info){    
			$sqlString ="update hz_doctorinfo set favoriteNum='".$info['rows']."' where hospitalId='".$info['hospitalId']."' and deptId='".$info['deptId']."' and doctorId='".$info['doctorId']."'";
			$sqlData = call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"entity");    
		}   
		
		$sqlString ="SELECT COUNT(1) as rows,hospitalId FROM hz_favoritedoctor WHERE STATUS=1 GROUP BY hospitalId ";
		$sqlDataList = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"list");    
		foreach($sqlDataList as $key=>$info){    
			$sqlString ="update hz_hospitalinfo set favoriteNum='".$info['rows']."' where hospitalId='".$info['hospitalId']."'";
			$sqlData = call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"entity");    
		}   
	}  
	

}

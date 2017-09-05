<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
class SyncHisController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	
	public function getSyncHisAction(){   
		$hospitalId =C('HOSPITALID');
		$hisUrl =C('HIS_URL');
		$maxRegDays =C('maxRegDays'); 
		$appKey= md5(C('APPKEY')."|".date("Y-m-d", time()));  
		//$this->getHospitalInfo($hospitalId,$hisUrl,$appKey);
		//$this->getDeptInfo($hospitalId,$hisUrl,$appKey);
		//$this->getDoctorInfo($hospitalId,$hisUrl,$appKey);
		//$this->getRegInfo($hospitalId,$hisUrl,$appKey,$maxRegDays);
		//$this->getTimeRegInfo($hospitalId,$hisUrl,$appKey);   
		//删除今天前的数据 
		$this->delRegList();
	}
	
	//删除今天前的数据
	public function delRegList() {   
		$sqlString ="DELETE FROM hz_reginfo 	WHERE regDate<DATE('".date("Y-m-d", time())."')";
		$sqlData = call_user_func(array($_ENV["dbDao"],"delete"),$sqlString,"entity");  
	}
	
	//医院信息查询接口
	public function getHospitalInfo($hospitalId,$hisUrl,$appKey) {  
		$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId></req>"; 
		$postUrl = $hisUrl."?c=NfyyApi&a=getHospitalInfo&appkey=".$appKey;  
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
		$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurlStr"),$postUrl,$postData);
		print_r($xmldata);
		foreach($xmldata as $key=>$value){   
			$sqlString ="select id from hz_deptinfo where hospitalId='$hospitalId' and deptId ='$value->deptId'";
			$sqlData = call_user_func(array($_ENV["dbDao"],"select"),$sqlString,"entity");   
			if ($sqlData) {  
				$sqlString ="update hz_deptinfo set deptId='$value->deptId',deptName='$value->deptName',parentId='$value->parentId',info='$value->desc' where id='".$sqlData['id']."'";
				call_user_func(array($_ENV["dbDao"],"update"),$sqlString,"return");  
			}else{  
				$sqlString ="insert into hz_deptinfo(hospitalId,deptId,deptName,parentId,info) VALUES('$hospitalId','$value->deptId','$value->deptName','$value->parentId','$value->desc')";
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
	
	
	//医生号源分时信息查询接口
	public function getTimeRegInfo($hospitalId,$hisUrl,$appKey) { 
		$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId>220701</deptId><doctorId>PJ1</doctorId><regDate>2015-10-15</regDate><timeFlag>2</timeFlag></req>"; 
	    $postUrl = $hisUrl."?c=NfyyApi&a=getTimeRegInfo&appkey=".$appKey; 
		$xmldata = call_user_func(array($_ENV["commonClass"],"sendDataByCurl"),$postUrl,$postData);
		
		$timeRegInfo=$xmldata;
		foreach($timeRegInfo as $key=>$tri){ 
			echo trim($tri->startTime)."<br/>";  
			echo trim($tri->endTime)."<br/>";  
			echo trim($tri->regTotalCount)."<br/>";   
			echo trim($tri->regLeaveCount)."<br/>";    
		}    
		
	}  
	
	

}

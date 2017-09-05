<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
class ApiController extends BaseController {
	
	public function IndexAction(){   
		$this->display();
	}
	 
	
	//调用基本接口
	public function BaseAction($funname) {    
		$apiservice = new ApiService(); 
		if(!C($funname)){ 
			call_user_func(array($_ENV[$commonClass],"urlErr"));
		}else{ 
			call_user_func(array($apiservice,"ApiEngine"),$funname,C($funname)); 
		} 
	} 
	
	
	
	/*//调用医院列表接口
	public function BaseAction($funname) {    
		$apiservice = new ApiService(); 
		if(!C('hospitalList')){ 
			echo('jsonp1({"error":"非法访问"})');
		}else{ 
			call_user_func(array($apiservice,$funname),C('hospitalList'));
		}
		//	$apiservice->hospitalListService();
		
		
		//print_r(array($apiservice,$funname));
		//最原始的代码 
		//call_user_func(array($apiservice,$this->$funname.'Service'));
		//call_user_func(array($controller,'BaseAction'),array($this->a));
		//call_user_func(array($controller,'BaseAction'),array($this->a));
		//$date["funname"] =$funname;
		
		//echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} */
	
	
	
	
	//会员注册接口
	public function registerUserAction() {  
		
		
		
		/*	$postData="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>1051</hospitalId></req>"; 
			$postUrl = C('HIS_URL').'doReqToHis.aspx?service=getHospitalInfo';  //接收xml数据的文件  
			$xmldata = $this->sendDataByCurl($postUrl,$postData);  
			$hospitalId = "1051"; */
		$this->AjaxReturn($xmldata);   
		
		//print_r($xmldata);
		//return  $xmldata;
		/*$data = $db->query("select * from hz_hospitalinfo where hospitalId = '$hospitalId'"); 
		if ($data) {   
			$adddata = array(
				'id' => $data['id'],
				'hospitalId' =>  $hospitalId,
				'hospitalaName' => trim(strval($xmldata->hospitalName)), 
				'addr' => trim(strval($xmldata->addr)),
				'tel' =>trim(strval($xmldata->tel)),
				'webSite' =>trim(strval($xmldata->webSite)),
				'hospLevel' =>trim(strval($xmldata->hospLevel)),
				'hospArea' =>trim(strval($xmldata->hospArea)),
				'info' =>trim(strval($xmldata->desc)),
				'maxRegDays' =>trim(strval($xmldata->maxRegDays)),
				'startRegTime' =>trim(strval($xmldata->startRegTime)),
				'stopRegTime' =>trim(strval($xmldata->stopRegTime)),
				'stopBookTimeM' =>trim(strval($xmldata->stopBookTimeM)),
				'stopBookTimeA' =>trim(strval($xmldata->stopBookTimeA)),    
				'createDate' => date("Y-m-d H:i:s", time())
				);
			
			//print_r($adddata);
			
			M('hospitalinfo')->save($adddata);
			$this->success('同步科室信息查询接口成功!', U('Hisws/index')); 
			//echo M()->getLastSql(); 
		} */
	}
	
	
	
	//会员注册接口
	public function loginUserAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需  
		$loginname = I('loginname','','htmlspecialchars,trim');
		$password = I('password','','htmlspecialchars,trim'); 
		
		//$Service->Service($parray,sqlArr.sql1);
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = array();  
		$patientuser = $db->query("select * from hz_patientuser where username = '$loginname' or phone ='$loginname'");   
		$patientuser =  $patientuser[0];  
		if (!$patientuser || ($patientuser['password'] != $this->get_password($password, $patientuser['encrypt']))) {  
			$date["status"]="0"; 
			$date["info"]="账号或密码错误";   
		}else{ 
			if ($patientuser['status']) { 
				$date["status"]="0"; 
				$date["info"]="用户被锁定！";  
			}else{
				$timestamp=$this->get_timestamp();  
				$token=$this->get_token($patientuser['id'],$timestamp); 
				$loginNum =$patientuser['loginNum']+1;  
				
				$update = $db->execute("update hz_patientuser set lastIp='".$this->get_client_ip()."',lastTime='".date('Y-m-d H:i:s')."',loginNum=$loginNum,timestamp='".$timestamp."',token='".$token."' where id=".$patientuser['id']);
				
				if($update){ 
					$date["status"]=1; 
					$date["info"]="登录成功";   
					$date["timestamp"]=$timestamp; 
					$date["token"]=$token;   
				}else{
					$date["status"]="0"; 
					$date["info"]="系统登录出错，请再登录";  
				}  
			}
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	
	
	//调用医院列表接口
	public function hospitalListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$hospitallist = $db->query("select hospitalId,hospitalName,hospLevel,hospPhoto,attentionNum,commentNum  from hz_hospitalinfo where status = '0' ");  
			if($hospitallist){ 
				$date["status"]="1"; 
				$date["info"]="正确";    
				$date["data"]=$hospitallist;
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，查询相关信息不存在";   
			}
		}
		
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	
	//验证时间戳和用户token
	function get_check() { 
		//验证方案
		$timestamp = I('timestamp','','htmlspecialchars,trim'); 
		$token = I('token','','htmlspecialchars,trim');  
		$date = array();  
		/*$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置 
				
		//除了注册和登录要验证timestamp和token 
		$checkuser = $db->query("select * from hz_patientuser where timestamp = '$timestamp' and token ='$token'");   
		$checkuser =  $checkuser[0];  
				
		if ($checkuser) {    
			if(((float)$this->get_timestamp()-(float)$this->get_timestamp_eq($checkuser["timestamp"]))>0){
				$timestamp=$this->get_timestamp();  
				$token=$this->get_token($patientuser['id'],$timestamp); 
				$update = $db->execute("update hz_patientuser set  timestamp='".$timestamp."',token='".$token."' where id=".$checkuser['id']);
				if($update){ 
					$date["datatoken"]["status"]=2; 
					$date["datatoken"]["info"]="间戳和用户token到时，请更新";   
					$date["datatoken"]["timestamp"]=$timestamp; 
					$date["datatoken"]["token"]=$token;    
				}else{
					$date["datatoken"]["status"]=1; 
					$date["datatoken"]["info"]="间戳和用户token正确";   
					$date["datatoken"]["timestamp"]=$timestamp; 
					$date["datatoken"]["token"]=$token;
				}  
				
			}else{
				$date["datatoken"]["status"]="1"; 
				$date["datatoken"]["info"]="间戳和用户token正确";   
				$date["datatoken"]["timestamp"]=$timestamp; 
				$date["datatoken"]["token"]=$token;   
			} 
		}else{  
			$date["datatoken"]["status"]=0; 
			$date["datatoken"]["info"]="间戳和用户token错误，非法操作！";
			$date["datatoken"]["timestamp"]=$timestamp; 
			$date["datatoken"]["token"]=$token;    
		}   */
		
		
		$date["datatoken"]["status"]=1; 
		$date["datatoken"]["info"]="间戳和用户token正确";   
		$date["datatoken"]["timestamp"]=$timestamp; 
		$date["datatoken"]["token"]=$token;
		
		return $date;
	}
	
	
	
	//调用查询医院列表接口
	public function searchHospitalAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalName = I('hospitalName','','htmlspecialchars,trim'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$hospitallist = $db->query("select hospitalId,hospitalName,hospLevel,hospPhoto,attentionNum,commentNum  from hz_hospitalinfo where hospitalName like '%$hospitalName%' and status = '0' ");  
			$date["data"]=$hospitallist; 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 


	
	//调用医院列表接口
	public function hospitalInfoAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置 
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$hospitalinfo = $db->query("select hospitalId,hospitalName,hospLevel,hospPhoto,info,attentionNum,commentNum  from hz_hospitalinfo where hospitalId='$hospitalId' and status = '0' ");   
			$hospitalinfo =  $hospitalinfo[0];  
			$date =$hospitalinfo; 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  
	
	//调用医院科室列表接口
	public function deptListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$deptlist = $db->query("SELECT hospitalId,deptId,deptName,parentId FROM hz_deptinfo WHERE  hospitalId='$hospitalId' ");   
			if($deptlist){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date["data"]=$deptlist;
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 

	//调用查询科室信息接口
	public function searchDeptAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$deptName = I('deptName','','htmlspecialchars,trim'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$deptlist = $db->query("SELECT hospitalId,deptId,deptName FROM hz_deptinfo WHERE  hospitalId='$hospitalId' and deptName like '%$deptName%' and parentId<>'-1' ");  
			
			if($deptlist){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date["data"]=$deptlist;
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据   
	} 

	//调用医院单科室信息接口
	public function deptInfoAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');
		$deptId = I('deptId',0,'intval');
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) {  
			$deptinfo = $db->query("SELECT d.hospitalId,h.hospitalName,d.deptId,d.deptName,d.info,d.attentionNum,d.commentNum,d.remark,d.deptPhoto FROM hz_deptinfo d,hz_hospitalinfo h WHERE d.hospitalId=h.hospitalId and d.hospitalId='$hospitalId' and d.deptId='$deptId' ");  
			
			if($deptinfo){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date =$deptinfo[0]; 
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 


	//调用医院科室列表接口
	public function doctorListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$deptId = I('deptId',0,'intval');  
		$pagenum = I('pagenum',0,'intval');  
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			if($deptId==0){
				$doctorlist = $db->query("SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.attentionNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and d.hospitalId='$hospitalId' limit $pagenum,1 ");  
				$date["deptName"]="全院";
			}else{
				$doctorlist = $db->query("SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.attentionNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and d.hospitalId='$hospitalId'  and d.deptId='$deptId' limit $pagenum,1 ");  			
				$deptinfo = $db->query("SELECT deptName FROM hz_deptinfo  WHERE  deptId='$deptId' "); 
				$date["deptName"]=$deptinfo[0]['deptName'];
			}
			
			if($doctorlist){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date["data"]=$doctorlist;
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		} 
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	
	//调用医院单医生信息接口
	public function doctorInfoAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');
		$deptId = I('deptId',0,'intval');
		$doctorId = I('doctorId','0','htmlspecialchars,trim');
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			$doctorinfo = $db->query("SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.attentionNum ,d.commentNum,d.info FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE d.hospitalId=h.hospitalId AND d.deptId=p.deptId  and d.hospitalId='$hospitalId' and d.deptId='$deptId' and d.doctorId='$doctorId' ");  
			
			if($doctorinfo){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date =$doctorinfo[0]; 
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 

	
	//调用医院科室列表接口
	public function regListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$deptId = I('deptId',0,'intval');   
		$regDate= I('regDate','','htmlspecialchars,trim'); 
		$doctorId = I('doctorId','','htmlspecialchars,trim');  
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			if($regDate==''){
				$regDate = date("Y-m-d", time());
			}else
			{
				//如果当前时间大于7天或小于今天的，返回时间出错
			}
			
			$deptinfo = $db->query("SELECT deptName FROM hz_deptinfo  WHERE  deptId='$deptId' ");   
			$date["deptName"]=$deptinfo[0]['deptName'];
			if($doctorId==''){
				$reglist = $db->query("SELECT r.hospitalId,r.deptId,r.doctorId,r.doctorName,d.title,r.regDate,r.regFee,r.treatFee,(r.regFee+r.treatFee)/100 AS allFee,r.timeFlag,CASE WHEN r.timeFlag = '1' THEN '上午' WHEN r.timeFlag = '2' THEN '下午' END AS timeDesc ,r.regleaveCount,d.title,d.doctorPhoto,d.attentionNum,d.commentNum FROM hz_reginfo r,hz_doctorinfo d WHERE r.hospitalId=d.hospitalId AND r.deptId=d.deptId AND r.doctorId=d.doctorId AND regStatus=1 and d.hospitalId='$hospitalId'  and d.deptId='$deptId'  AND r.regDate='$regDate' ORDER BY  d.doctorId, r.timeFlag");
			}else{
				$reglist = $db->query("SELECT r.hospitalId,r.deptId,r.doctorId,r.doctorName,d.title,r.regDate,r.regFee,r.treatFee,(r.regFee+r.treatFee)/100 AS allFee,r.timeFlag,CASE WHEN r.timeFlag = '1' THEN '上午' WHEN r.timeFlag = '2' THEN '下午' END AS timeDesc ,r.regleaveCount,d.title,d.doctorPhoto,d.attentionNum,d.commentNum FROM hz_reginfo r,hz_doctorinfo d WHERE r.hospitalId=d.hospitalId AND r.deptId=d.deptId AND r.doctorId=d.doctorId AND regStatus=1 and d.hospitalId='$hospitalId'  and d.deptId='$deptId'  and d.doctorId='$doctorId'   AND r.regDate='$regDate' ORDER BY  d.doctorId, r.timeFlag");
			}
			
			
			if($reglist){
				$date["status"]="1"; 
				$date["info"]="正确";  
				$date["data"]=$reglist; 
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 


	//调用医院科室列表接口
	public function timeRegInfoAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$deptId = I('deptId',0,'intval');   
		$regDate= I('regDate','','htmlspecialchars,trim'); 
		$doctorId = I('doctorId','','htmlspecialchars,trim');  
		$timeFlag = I('timeFlag',0,'intval');  
		$allFee = I('allFee',0,'htmlspecialchars,trim');  
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			if($regDate==''){
				$regDate = date("Y-m-d", time());
			}else
			{
				//如果当前时间大于7天或小于今天的，返回时间出错
			}
			
			
			$postdata="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>$hospitalId</hospitalId><deptId>$deptId</deptId><doctorId>$doctorId</doctorId><regDate>$regDate</regDate><timeFlag>$timeFlag</timeFlag></req>"; 
			$url = C('HIS_URL').'doReqToHis.aspx?service=getTimeRegInfo';  //接收xml数据的文件   
			$xmldata = $this->SendDataByCurl($url,$postdata); 
			$timeRegInfo=array();
			$timeRegInfoList = $xmldata->timeRegInfo; 
			foreach($timeRegInfoList as $key=>$tri){  
				$timeRegInfo['allFee']=$allFee;
				$timeRegInfo['startTime']=trim($tri->startTime);  
				$timeRegInfo['endTime']=trim($tri->endTime);  
				$timeRegInfo['regTotalCount']=trim($tri->regTotalCount);   
				$timeRegInfo['regLeaveCount']=trim($tri->regLeaveCount);  
				$date["data"][] =$timeRegInfo;  
			} 
			
			if($timeRegInfo){
				$date["status"]="1"; 
				$date["info"]="正确";  
				//$date["data"]=$timeRegInfo; 
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
				$date["data"]="";
			}  
		}
		
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 

	
	//调用添加医院患者信息接口
	public function addPatientAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');   
		$ownership= I('ownership','','htmlspecialchars,trim'); 
		$trueName= I('trueName','','htmlspecialchars,trim');  
		$idNo= I('idNo','','htmlspecialchars,trim'); 
		$sex= I('sex','','htmlspecialchars,trim');  
		$phone= I('phone','','htmlspecialchars,trim'); 
		$address= I('address','','htmlspecialchars,trim');  
		$cardNo= I('cardNo','','htmlspecialchars,trim');    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			$patinfo = $db->query("SELECT * FROM hz_patientindex  WHERE hospitalId='$hospitalId' and trueName='$trueName' and idNo='$idNo' ");    
			
			if($patinfo){
				$date["status"]="0"; 
				if($patinfo[0]['cardNo']){
					$date["info"]="添加失败，病人信息已存在！";				
				}else{
					$date["info"]="添加失败，病人信息已存在,您的诊疗卡未绑定！";
				}
			}else{ 
				
				//除了注册和登录要验证timestamp和token 
				$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
				$userid =  $userid[0]["id"];   
				
				//得到身份证和生日
				$res = $this->getIDCardInfo($idNo);
				if($res['error']=="2"){
					$birthDay = $res['birthday'];
					$patresult= $db->execute("INSERT INTO hz_patientindex (patientUserId,hospitalId, trueName,idNo,sex,phone,address,cardNo,ownership,birthDay) VALUES ('$userid','$hospitalId','$trueName','$idNo','$sex','$phone','$address','$cardNo','$ownership','$birthDay')");
					if($patresult){
						$date["status"]="1"; 
						$date["info"]="添加成功";
					}else{
						$date["status"]="0"; 
						$date["info"]="添加失败，请再试试";
					}
				}else{ 
					$date["status"]="0"; 
					$date["info"]="身份证格式错误"; 
				}
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 


	//调用添加医院患者信息接口
	public function patientListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			$patinfo = $db->query("SELECT id,hospitalId, trueName,idNo,sex,phone,address,cardNo FROM hz_patientindex  WHERE hospitalId='$hospitalId'");     
			if($patinfo){
				$date["status"]="1";  
				$date["info"]="成功,病人信息";
				$date["data"]=$patinfo;
			}else{  
				$date["status"]="0"; 
				$date["info"]="您没有添加该医院的患者信息及诊疗卡"; 
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  
	
	
	
	


	//会诊申请列表信息接口
	public function historyListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
			$userid =  $userid[0]["id"];   
			
			$historylist = $db->query("SELECT h.hospitalName,p.deptName,d.doctorName,d.title,d.doctorphoto,a.regDate,a.id,a.orderId,a.orderIdHIS,a.patientIndexId FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND a.patientIndexId IN (SELECT id FROM hz_patientindex WHERE patientUserId='$userid')");     
			if($historylist){
				$date["status"]="1";  
				$date["info"]="查询成功,预约就诊列表信息";
				$date["data"]=$historylist;
			}else{  
				$date["status"]="0"; 
				$date["info"]="您没有,预约就诊列表信息"; 
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  
	

	

	//挂号接口接口
	public function addOrderAction() {

		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');   
		$deptId = I('deptId',0,'intval');   
		$doctorId = I('doctorId','','htmlspecialchars,trim');   
		$regDate= I('regDate','','htmlspecialchars,trim'); 
		$timeFlag= I('timeFlag','','htmlspecialchars,trim');  
		$startTime = I('startTime','','htmlspecialchars,trim'); 
		$endTime= I('endTime','','htmlspecialchars,trim');  
		$patientindexid= I('patientindexid','','htmlspecialchars,trim'); 
		$regFee= I('regFee',0,'htmlspecialchars,trim');
		$treatFee= I('treatFee',0,'htmlspecialchars,trim'); 
		
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) {  
			//读取挂号人信息
			$patientindex = $db->query("select * from hz_patientindex where id = '".$patientindexid."'");   
			
			$patientindex =  $patientindex[0];  
			
			
			if($patientindex)
			{  
				//读取操作人信息
				$patientuser = $db->query("select * from hz_patientuser where id = '".$patientindex['patientUserId']."'");  
				$orderId =$this->get_timestamp()."_".$patientindex['id'];
				$orderTime =date("Y-m-d H:i:s", time());
				$sex = $patientindex['sex']== "男" ? "M" : "F"; 
				
				//先创建订单
				$poid= $db->executeinsert("INSERT INTO hz_appointsorder (orderId,hospitalId,deptId,doctorId,regDate,timeFlag,startTime,endTime,patientIndexId,orderTime,fee,treatfee) VALUES ('$orderId','$hospitalId','$deptId','$doctorId','$regDate','$timeFlag','$startTime','$endTime','".$patientindex['id']."','$orderTime','$regFee','$treatFee')");
				if($poid){
					$postdata="<?xml version=\"1.0\" encoding=\"UTF-8\"?><req>";
					$postdata .="<orderId>".$orderId."</orderId><hospitalId>$hospitalId</hospitalId><deptId>$deptId</deptId><doctorId>$doctorId</doctorId>"; 
					$postdata .="<regDate>$regDate</regDate><timfFlag>$timeFlag</timfFlag><startTime>$startTime</startTime><endTime>$endTime</endTime><regType>1</regType>";
					$postdata .="<userIdCard>".$patientindex['idNo']."</userIdCard><userJKK></userJKK><userSMK/><userYBK/><userName>".$patientindex['trueName']."</userName><userAddress>".$patientindex['address']."</userAddress>";
					$postdata .="<userGender>$sex</userGender><userMobile>".$patientindex['phone']."</userMobile><userBirthday>".$patientindex['birthDay']."</userBirthday><operIdCard>xxx</operIdCard>";
					$postdata .="<operName>xxx</operName><operMobile>xxx</operMobile><userChoice>2</userChoice><agentId/><orderType>3</orderType>"; 
					$postdata .="<orderTime>$orderTime</orderTime><fee>$regFee</fee><treatfee>$treatFee</treatfee> ";
					$postdata .="</req>";

					$url = C('HIS_URL').'doReqToHis.aspx?service=addOrder';  //接收xml数据的文件   
					$xmldata = $this->SendDataByCurl($url,$postdata); 
					if($xmldata){
						//返回信息 
						$date["status"]=trim($xmldata->resultCode)=="1" ? "0":"1";  
						$date["info"]=trim($xmldata->resultDesc);  
						$update = $db->execute("update hz_appointsorder set  resultCode='".$date["status"]."',resultDesc='".$date["info"]."',orderIdHIS='".trim($xmldata->orderIdHIS)."',userFlag='".trim($xmldata->userFlag)."' where id=".$poid);
					}else{
						$date["status"]="0"; 
						$date["info"]="挂号失败，与HIS连接出现问题";
					}
					
				}else{
					//生成订单失败
					$date["status"]="0"; 
					$date["info"]="生成订单失败，请再试试";
				}
			}
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  
	

	//调用用药接口
	public function medicineListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			 
			$medicineinfo = $db->query("SELECT * FROM hz_medicine  WHERE hospitalId='$hospitalId' ");   
			//获得患者就诊处方列表
			if($medicineinfo){
				
				foreach($medicineinfo as $key=>$medicine){  
					$date["data"]["medicine"] =$medicine;  
					
					//获得处方明细
					$medicineitem = $db->query("SELECT * FROM hz_medicineitem WHERE medicineId='".$medicine['id']."'");   
					foreach($medicineitem as $key=>$meditem){  
						$date["data"]["medicine"]["medicineitem"][] =$meditem;  
						
						//获得处方执行计划
						$schedulegroup = $db->query("SELECT * FROM hz_schedulegroup WHERE medicineItemId='".$meditem['id']."'");   
						foreach($schedulegroup as $key=>$medsg){  
							$date["data"]["medicine"]["medicineitem"]["schedulegroup"] =$medsg;  
							//获得处方执行计划
							$medicineschedule = $db->query("SELECT * FROM hz_medicineschedule WHERE scheduleGroupId='".$medsg['id']."'");   
							foreach($medicineschedule as $key=>$medsch){  
								$date["data"]["medicine"]["medicineitem"]["schedulegroup"]["schedule"][] = $medsch;  
							}
						}
					}
				}  
				
			}else{
				
			}
			
			
			if($date["data"]){
				$date["status"]="1"; 
				$date["info"]="正确";   
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	
	
	//调用用药接口
	public function addMedicineAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');  
		$status= I('status',1,'intval'); 
		$diagnos= I('diagnos','','htmlspecialchars,trim');    
		$medicineDate= I('medicineDate','','htmlspecialchars,trim');  //服用开始时间
		//以上是药店添加的资料
		
		 
		$phamName= I('phamName','','htmlspecialchars,trim');  //药品名称
		$frequency= I('frequency','','htmlspecialchars,trim'); //用药频次
		$days= I('days',0,'intval');  //服药天数
		$nUnits= I('nUnits','','htmlspecialchars,trim'); //药品最小单位
		 
		$consumptionHoursString= I('consumptionHoursString','','htmlspecialchars,trim'); //服用频次 8:00.9:00
		$quantityString= I('quantityString','','htmlspecialchars,trim');   //服用药量 1.00,1.00
		$everyXDays= I('everyXDays',0,'intval'); 
		    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			
			$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
			$userid =  $userid[0]["id"];   
			$createDate = date("Y-m-d h:i:s", time());
			$medid= $db->executeinsert("INSERT INTO  hz_medicine (hospitalId,deptId,doctorId,patientUserId,diagnos,status,medicineDate,createDate) VALUES ('0','0','0','$userid','$diagnos','1','$medicineDate','$createDate')");
			
			 
			if($medid){
				
				$itemid= $db->executeinsert("INSERT INTO  hz_medicineitem (medicineId,phamName,frequency,days,numbers,nUnits,mdeicineTime) VALUES ('$medid','$phamName','$frequency','$days','$numbers','$nUnits','$medicineDate')");
				if($itemid){
					$groupid= $db->executeinsert("INSERT INTO  hz_schedulegroup (medicineItemId,startDate,consumptionHoursString,quantityString,everyXDays,days,dayConsumption,status,created) VALUES ('$itemId','$medicineDate','$consumptionHoursString','$quantityString','$everyXDays','$days','0','active','$createDate')");
					 
				}
			
				
				
			}else{
				
			}
			
			
			if($date["data"]){
				$date["status"]="1"; 
				$date["info"]="正确";   
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	//调用用药接口
	public function editMedicineAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval'); 
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
			$userid =  $userid[0]["id"];   
			 
			$patresult= $db->executeinsert("INSERT INTO  hz_medicine (hospitalId,deptId,doctorId,patientUserId,ownership,trueName,idNo,sex,phone,address,patientId,patientInHosNum,patientAge,nativePlace,patientDept,patientInfo,simpleMed,baseDiag,conReason,conRequesDate,urgent,remoteType,remoteserver) VALUES ('$hospitalId','$deptId','$doctorId','$userid','$ownership','$trueName','$idNo','$sex','$phone','$address','$patientId','$patientInHosNum','$patientAge','$nativePlace','$patientDept','$patientInfo','$simpleMed','$baseDiag','$conReason','$conRequesDate'	,'$urgent','$remoteType','$remoteserver')");
			
		
			//获得患者就诊处方列表
			if($medicineinfo){
				
				foreach($medicineinfo as $key=>$medicine){  
					$date["data"]["medicine"] =$medicine;  
					
					//获得处方明细
					$medicineitem = $db->query("SELECT * FROM hz_medicineitem WHERE medicineId='".$medicine['id']."'");   
					foreach($medicineitem as $key=>$meditem){  
						$date["data"]["medicine"]["medicineitem"][] =$meditem;  
						
						//获得处方执行计划
						$schedulegroup = $db->query("SELECT * FROM hz_schedulegroup WHERE medicineItemId='".$meditem['id']."'");   
						foreach($schedulegroup as $key=>$medsg){  
							$date["data"]["medicine"]["medicineitem"]["schedulegroup"] =$medsg;  
							//获得处方执行计划
							$medicineschedule = $db->query("SELECT * FROM hz_medicineschedule WHERE scheduleGroupId='".$medsg['id']."'");   
							foreach($medicineschedule as $key=>$medsch){  
								$date["data"]["medicine"]["medicineitem"]["schedulegroup"]["schedule"][] = $medsch;  
							}
						}
					}
				}  
				
			}else{
				
			}
			
			
			if($date["data"]){
				$date["status"]="1"; 
				$date["info"]="正确";   
			}else{
				$date["status"]="0"; 
				$date["info"]="错误，相关信息查找不到";
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	//添加申请会诊信息接口
	public function addRemoteApplyAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');   
		$deptId= I('deptId',0,'intval'); 
		$doctorId= I('doctorId','','htmlspecialchars,trim');    
		$ownership= I('ownership','','htmlspecialchars,trim'); 
		$trueName= I('trueName','','htmlspecialchars,trim');  
		$idNo= I('idNo','','htmlspecialchars,trim'); 
		$sex= I('sex','','htmlspecialchars,trim');  
		$phone= I('phone','','htmlspecialchars,trim'); 
		$address= I('address','','htmlspecialchars,trim');   
		$patientId= I('patientId','','htmlspecialchars,trim'); 
		$patientInHosNum= I('patientInHosNum','','htmlspecialchars,trim');   
		$patientAge= I('patientAge','','htmlspecialchars,trim'); 
		$nativePlace= I('nativePlace','','htmlspecialchars,trim');   
		$patientDept= I('patientDept','','htmlspecialchars,trim'); 
		$patientInfo= I('patientInfo','','htmlspecialchars,trim');   
		$simpleMed= I('simpleMed','','htmlspecialchars,trim');   
		$baseDiag= I('baseDiag','','htmlspecialchars,trim');   
		$conReason= I('conReason','','htmlspecialchars,trim');
		$conRequesDate= I('conRequesDate','','htmlspecialchars,trim');   
		$urgent= I('urgent','','htmlspecialchars,trim');   
		$remoteType= I('remoteType','','htmlspecialchars,trim');   
		$remoteserver= I('remoteserver','','htmlspecialchars,trim');   
		
		  
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			  
				//得到身份证和生日
			$res = $this->getIDCardInfo($idNo);
			if($res['error']=="2"){
				//除了注册和登录要验证timestamp和token 
				$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
				$userid =  $userid[0]["id"];   
				$patresult= $db->execute("INSERT INTO  hzremote.hz_consultation (hospitalId,deptId,doctorId,patientUserId,ownership,trueName,idNo,sex,phone,address,patientId,patientInHosNum,patientAge,nativePlace,patientDept,patientInfo,simpleMed,baseDiag,conReason,conRequesDate,urgent,remoteType,remoteserver) VALUES ('$hospitalId','$deptId','$doctorId','$userid','$ownership','$trueName','$idNo','$sex','$phone','$address','$patientId','$patientInHosNum','$patientAge','$nativePlace','$patientDept','$patientInfo','$simpleMed','$baseDiag','$conReason','$conRequesDate'	,'$urgent','$remoteType','$remoteserver')");
					if($patresult){
						$date["status"]="1"; 
						$date["info"]="添加成功";
					}else{
						$date["status"]="0"; 
						$date["info"]="添加失败，请再试试";
					}
			}else{ 
				$date["status"]="0"; 
				$date["info"]="身份证格式错误"; 
			} 
		} 
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 
	
	
	
	//修改申请会诊信息接口
	public function editRemoteApplyAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$id = I('id',0,'intval');   
		$hospitalId = I('hospitalId',0,'intval');   
		$deptId= I('deptId',0,'intval'); 
		$doctorId= I('doctorId','','htmlspecialchars,trim');    
		$ownership= I('ownership','','htmlspecialchars,trim'); 
		$trueName= I('trueName','','htmlspecialchars,trim');  
		$idNo= I('idNo','','htmlspecialchars,trim'); 
		$sex= I('sex','','htmlspecialchars,trim');  
		$phone= I('phone','','htmlspecialchars,trim'); 
		$address= I('address','','htmlspecialchars,trim');   
		$patientId= I('patientId','','htmlspecialchars,trim'); 
		$patientInHosNum= I('patientInHosNum','','htmlspecialchars,trim');   
		$patientAge= I('patientAge','','htmlspecialchars,trim'); 
		$nativePlace= I('nativePlace','','htmlspecialchars,trim');   
		$patientDept= I('patientDept','','htmlspecialchars,trim'); 
		$patientInfo= I('patientInfo','','htmlspecialchars,trim');   
		$simpleMed= I('simpleMed','','htmlspecialchars,trim');   
		$baseDiag= I('baseDiag','','htmlspecialchars,trim');   
		$conReason= I('conReason','','htmlspecialchars,trim');
		$conRequesDate= I('conRequesDate','','htmlspecialchars,trim');   
		$urgent= I('urgent','','htmlspecialchars,trim');   
		$remoteType= I('remoteType','','htmlspecialchars,trim');   
		$remoteserver= I('remoteserver','','htmlspecialchars,trim');   
		
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			//得到身份证和生日
			$res = $this->getIDCardInfo($idNo);
			if($res['error']=="2"){
				//除了注册和登录要验证timestamp和token 
				$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
				$userid =  $userid[0]["id"];   
				$patresult= $db->execute("update hzremote.hz_consultation set hospitalId='$hospitalId',deptId='$deptId',doctorId='$doctorId',ownership='$ownership',trueName='$trueName',idNo='$idNo',sex='$sex',phone='$phone',address='$address',patientId='$patientId',patientInHosNum='$patientInHosNum',patientAge='$patientAge',nativePlace='$nativePlace',patientDept='$patientDept',patientInfo='$patientInfo',simpleMed='$simpleMed',baseDiag='$baseDiag',conReason='$conReason',conRequesDate='$conRequesDate',urgent='$urgent',remoteType='$remoteType',remoteserver='$remoteserver' where id='$id' and patientUserId='$userid'"); 
				if($patresult){
					$date["status"]="1"; 
					$date["info"]="更新成功";
				}else{
					$date["status"]="0"; 
					$date["info"]="更新失败，请再试试";
				}
			}else{ 
				$date["status"]="0"; 
				$date["info"]="身份证格式错误"; 
			} 
		} 
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	} 



	//会诊申请列表信息接口
	public function remoteApplyListAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$hospitalId = I('hospitalId',0,'intval');    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			
			$userid = $db->query("select id from hz_patientuser where timestamp = '".$date["datatoken"]["timestamp"]."' and token ='".$date["datatoken"]["token"]."'");   
			$userid =  $userid[0]["id"];   
			
			$coninfo = $db->query("SELECT c.id,h.hospitalName,p.deptName,d.doctorName,d.title,c.conRequesDate,c.conState FROM hzremote.hz_consultation c,hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE c.hospitalId=h.hospitalId AND c.deptId=p.deptId AND c.doctorId=d.doctorId AND p.deptId=d.deptId AND c.patientUserId='$userid'");     
			if($coninfo){
				$date["status"]="1";  
				$date["info"]="成功,会诊申请列表信息";
				$date["data"]=$coninfo;
			}else{  
				$date["status"]="0"; 
				$date["info"]="您没有会诊申请列表信息"; 
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  
	
	//会诊申请列表信息接口
	public function remoteApplyDetailAction() {   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : ''; //jsonp回调参数，必需   
		$id = I('id',0,'intval');    
		
		$db = M();  //获取数据库对象，前提是在入口文件配好数据库相关的配置
		$date = $this->get_check();    
		if($date["datatoken"]["status"]>=1) { 
			 
			$coninfo = $db->query("SELECT *  FROM hzremote.hz_consultation c  WHERE c.id='$id'");     
			if($coninfo){
				$date["status"]="1";  
				$date["info"]="查询成功,会诊申请详细信息";
				$date["data"]=$coninfo;
			}else{  
				$date["status"]="0"; 
				$date["info"]="查询失败，您查询会诊申请信息没有资料"; 
			} 
		}
		echo $callback . '(' . json_encode($date) .')';  //返回格式，必需 json 数据  
	}  



}

<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/**
* 用于在配置流的列表
* 命名规则 I和D要注册大小写
* 命名规则 I_name  I是通过URL获取内容
* 命名规则 D_name  D是通过上一条数据获取内容 verifi
* 'demo'    => array(
	 '{"nodeType":"data|pass|if|verifi",parameter:"[para1,pa]","resultType":"autoid|entity|boolean|return|list|rows","sqlType":"select|update|insert|delete","sqlString":"select hospitalId,hospitalName,hospLevel,hospPhoto,favoriteNum,commentNum  from hz_hospitalinfo where status =0"}'
   ),
*  nodeType:pass 为验证如果返回结果为空则可以进入下一步
*  nodeType:if  如为if 在parameter里只能有一个为IF|参数  sqlString sql语句里 <if>条件</if> 
* "nodeType":"verifi"  如果节点为verifi 那就执行下面两条语句，一个节点返回true的执行，一个节点返回为false的执行
* 该方法是要写整体方法，不能用公用的
*/
$otherConfig = array( 
	  //添加药品表
	  'medicineAdd'   => array(
			'{"nodeType":"step1","parameter":"M_id,I_customerFamilyId,I_startDate","resultType":"entity","sqlType":"select","sqlString":"SELECT id as autoid FROM hz_medicine t WHERE  t.customerFamilyId=\'{I_customerFamilyId}\' AND t.customerUserId=\'{M_id}\' AND DATE(t.medicineDate) = DATE(\'{I_startDate}\' AND OWNER=\'2\' )","infoString":"今天已添加过处方不能再添加"}', 
			'{"nodeType":"step2","parameter":"M_id,I_customerFamilyId,I_diagnose,I_startDate,F_timenow,I_owner,I_source","resultType":"autoid","sqlType":"insert","sqlString":"INSERT INTO hz_medicine (customerFamilyId,customerUserId,diagnose,medicineDate,createDate,owner,source) VALUES(\'{I_customerFamilyId}\',\'{M_id}\',\'{I_diagnose}\',\'{I_startDate}\',\'{F_timenow}\',\'2\',\'{I_source}\')","infoString":"添加处方失败"}',
			'{"nodeType":"step3","parameter":"D_autoid,I_drugName,I_frequency,I_days,I_doseType,I_administration,I_info,I_startDate","resultType":"autoid","sqlType":"insert","sqlString":"INSERT INTO hz_medicineitem (medicineId,drugName,frequency,days,doseType,administration,info,medicineDate) VALUES (\'{D_autoid}\',\'{I_drugName}\',\'{I_frequency}\',\'{I_days}\',\'{I_doseType}\',\'{I_administration}\',\'{I_info}\',\'{I_startDate}\')","infoString":"添加药品失败"}',
			'{"nodeType":"step4","parameter":"D_autoid,I_startDate,I_consumptionHoursString,I_quantityString,I_everyXDays,I_days,I_dayConsumption,F_timenow,I_doseType,I_daysToTake","resultType":"autoid","sqlType":"insert","sqlString":"INSERT INTO hz_medicineitemschedulegroup (medicineItemId,startDate,consumptionHoursString,quantityString,everyXDays,days,dayConsumption,status,created,doseType,daysToTake) VALUES(\'{D_autoid}\',\'{I_startDate}\',\'{I_consumptionHoursString}\',\'{I_quantityString}\',\'{I_everyXDays}\',\'{I_days}\',\'0\',\'active\',\'{F_timenow}\',\'{I_doseType}\',\'{I_daysToTake}\')","infoString":"添加药品计划组失败"}',
			'{"nodeType":"step5","parameter":"","resultType":"return","sqlType":"insert","sqlString":"INSERT INTO hz_medicineschedule (scheduleGroupId,actualDateTime,originalDateTime,status,medicineItemId,consumptionHours,quantity,doseType) VALUES","infoString":"添加药品计划失败"}',
			), 
		
		//短信注册码验证
		'registerVerifyCode' => array(  
			'{"nodeType":"data","parameter":"I_phone,F_randomnum,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_verifysmscode(phone,type,code,createtime) VALUES(\'{I_phone}\',\'0\',\'{F_randomnum}\',\'{F_timenow}\')","infoString":"添加数据失败"}',
			'{"nodeType":"data","parameter":"D_autoid","resultType":"entity","sqlType":"select","sqlString":"select id,phone,code from hz_verifysmscode where id=\'{D_autoid}\'","infoString":"添加数据失败"}',
			),  
		
		//短信忘记密码验证
		'resetPwdVerifyCode' => array(    
			'{"nodeType":"data","parameter":"I_phone","resultType":"entity","sqlType":"select","sqlString":"select phone from hz_customeruser where phone = \'{I_phone}\'","infoString":"用户名不正确"}',
			'{"nodeType":"data","parameter":"I_phone,F_randomnum,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_verifysmscode(phone,type,code,createtime) VALUES(\'{I_phone}\',\'1\',\'{F_randomnum}\',\'{F_timenow}\')","infoString":"添加数据失败"}',
			'{"nodeType":"data","parameter":"D_autoid","resultType":"entity","sqlType":"select","sqlString":"select id,phone,code from hz_verifysmscode where id=\'{D_autoid}\'","infoString":"添加数据失败"}',
			),  
		
		//添加预约
		'appointsOrder' => array( 
			'{"nodeType":"sql","parameter":"F_orderId,I_hospitalId,I_deptId,I_doctorId,I_regDate,I_timeFlag,I_startTime,I_endTime,M_id,I_customerFamilyId,F_timenow,I_regFee,I_treatFee","resultType":"autoid","dataType":"","sqlType":"insert","sqlString":"INSERT INTO hz_appointsorder (orderId,hospitalId,deptId,doctorId,regDate,timeFlag,startTime,endTime,customerUserId,customerFamilyId,orderTime,fee,treatfee) VALUES (\'{F_orderId}\',\'{I_hospitalId}\',\'{I_deptId}\',\'{I_doctorId}\',\'{I_regDate}\',\'{I_timeFlag}\',\'{I_startTime}\',\'{I_endTime}\',\'{M_id}\',\'{I_customerFamilyId}\',\'{F_timenow}\',\'{I_regFee}\',\'{I_treatFee}\')","infoString":"添加数据失败"}', 
			'{"nodeType":"sql","parameter":"M_id,D_autoid","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,orderId,orderTime FROM hz_appointsorder WHERE id=\'{D_autoid}\' AND customerUserId=\'{M_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  trueName,phone,idNo,birthDay,nation,address,CASE WHEN sex = \'男\' THEN \'M\' WHEN sex = \'女\' THEN \'F\' END AS sex,patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"sql","parameter":"M_id","resultType":"entity","dataType":"add","sqlType":"select","sqlString":" SELECT  trueName AS operName,phone AS operMobile,idNo AS operIdCard  FROM hz_customerfamily f where f.customerUserId=\'{M_id}\'","infoString":"查询不到成员数据"}', 
			'{"nodeType":"xml","parameter":"D_orderId,I_hospitalId,I_scheduleCode,I_deptId,I_doctorId,I_regDate,I_timeFlag,I_startTime,I_endTime,D_idNo,D_trueName,D_address,D_sex,D_phone,D_birthDay,D_orderTime,D_patientId,I_regFee,I_treatFee,D_operIdCard,D_operName,D_operName","funcName":"addOrder","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><orderId>{D_orderId}</orderId><hospitalId>{I_hospitalId}</hospitalId><scheduleCode>{I_scheduleCode}</scheduleCode><deptId>{I_deptId}</deptId><doctorId>{I_doctorId}</doctorId><regDate>{I_regDate}</regDate><timfFlag>{I_timeFlag}</timfFlag><startTime>{I_startTime}</startTime><endTime>{I_endTime}</endTime><userIdCard>{D_idNo}</userIdCard><userName>{D_trueName}</userName><userAddress>{D_address}</userAddress><userGender>{D_sex}</userGender><userMobile>{D_phone}</userMobile><userBirthday>{D_birthDay}</userBirthday><operIdCard>{D_operIdCard}</operIdCard><operName>{D_operName}</operName><operMobile>{D_operName}</operMobile><patType>0</patType><patCardId>{D_patientId}</patCardId><orderTime>{D_orderTime}</orderTime><fee>{I_regFee}</fee><treatfee>{I_treatFee}</treatfee></req>","replaceXmlData":"resultCode,resultDesc,orderIdHIS,userFlag","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"D_id,D_resultCode,D_resultDesc,D_patientId,D_orderIdHIS,D_userFlag","resultType":"return","dataType":"add","sqlType":"update","sqlString":"UPDATE hz_appointsorder SET resultCode = \'{D_resultCode}\',resultDesc = \'{D_resultDesc}\',orderIdHIS = \'{D_orderIdHIS}\',patientId=\'{D_patientId}\',userFlag = \'{D_userFlag}\' WHERE id = \'{D_id}\'","infoString":"更新数据失败"}', 			
			'{"nodeType":"sql","parameter":"D_id,D_patientId,I_regDate,I_startTime,I_endTime,D_orderIdHIS,M_id,I_customerFamilyId,F_timenow","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_pushtask (parameter,title,content,customerUserId,customerFamilyId,eventType,pushTime,createTime) values(\'{D_id}\',\'预约成功\',\'就诊时间{I_regDate} {I_startTime}到{I_endTime} 。预约流水号：{D_orderIdHIS},病人ID：{D_patientId}。\',\'{M_id}\',\'{I_customerFamilyId}\',\'3\',\'{I_regDate} {I_startTime}:00\',\'{F_timenow}\')","infoString":"查询无数据"}', 
			'{"nodeType":"sql","parameter":"D_id","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT orderId,resultDesc,orderIdHIS,userFlag FROM hz_appointsorder WHERE id=\'{D_id}\'","infoString":"查询无数据"}', 
			
			), 

		 'appointsOrderNew' => array( 
			'{"nodeType":"sql","parameter":"F_orderId,I_hospitalId,I_deptId,I_doctorId,I_regDate,I_timeFlag,I_startTime,I_endTime,M_id,I_customerFamilyId,F_timenow,I_regFee,I_treatFee","resultType":"autoid","dataType":"","sqlType":"insert","sqlString":"INSERT INTO hz_appointsorder (orderId,hospitalId,deptId,doctorId,regDate,timeFlag,startTime,endTime,customerUserId,customerFamilyId,orderTime,fee,treatfee) VALUES (\'{F_orderId}\',\'{I_hospitalId}\',\'{I_deptId}\',\'{I_doctorId}\',\'{I_regDate}\',\'{I_timeFlag}\',\'{I_startTime}\',\'{I_endTime}\',\'{M_id}\',\'{I_customerFamilyId}\',\'{F_timenow}\',\'{I_regFee}\',\'{I_treatFee}\')","infoString":"添加数据失败"}', 
			'{"nodeType":"sql","parameter":"M_id,D_autoid","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,orderId,orderTime FROM hz_appointsorder WHERE id=\'{D_autoid}\' AND customerUserId=\'{M_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  trueName,phone,idNo,birthDay,nation,address,CASE WHEN sex = \'男\' THEN \'M\' WHEN sex = \'女\' THEN \'F\' END AS sex FROM hz_customerfamily f WHERE f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\'","infoString":"查询不到成员数据"}', 
			'{"nodeType":"sql","parameter":"M_id","resultType":"entity","dataType":"add","sqlType":"select","sqlString":" SELECT  trueName AS operName,phone AS operMobile,idNo AS operIdCard  FROM hz_customerfamily f where f.customerUserId=\'{M_id}\'","infoString":"查询不到成员数据"}', 
			'{"nodeType":"xml","parameter":"D_orderId,I_hospitalId,I_scheduleCode,I_deptId,I_doctorId,I_regDate,I_timeFlag,I_startTime,I_endTime,D_idNo,D_trueName,D_address,D_sex,D_phone,D_birthDay,D_orderTime,D_patientId,I_regFee,I_treatFee,D_operIdCard,D_operName,D_operName","funcName":"addOrder","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><orderId>{D_orderId}</orderId><hospitalId>{I_hospitalId}</hospitalId><scheduleCode>{I_scheduleCode}</scheduleCode><deptId>{I_deptId}</deptId><doctorId>{I_doctorId}</doctorId><regDate>{I_regDate}</regDate><timfFlag>{I_timeFlag}</timfFlag><startTime>{I_startTime}</startTime><endTime>{I_endTime}</endTime><userIdCard>{D_idNo}</userIdCard><userName>{D_trueName}</userName><userAddress>{D_address}</userAddress><userGender>{D_sex}</userGender><userMobile>{D_phone}</userMobile><userBirthday>{D_birthDay}</userBirthday><operIdCard>{D_operIdCard}</operIdCard><operName>{D_operName}</operName><operMobile>{D_operName}</operMobile><patType>1</patType><patCardId>{D_patientId}</patCardId><orderTime>{D_orderTime}</orderTime><fee>{I_regFee}</fee><treatfee>{I_treatFee}</treatfee></req>","replaceXmlData":"resultCode,resultDesc,orderIdHIS,userFlag","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"D_id,D_resultCode,D_resultDesc,D_orderIdHIS,D_patientId,D_userFlag","resultType":"return","dataType":"add","sqlType":"update","sqlString":"UPDATE hz_appointsorder SET resultCode = \'{D_resultCode}\',resultDesc = \'{D_resultDesc}\',orderIdHIS = \'{D_orderIdHIS}\',patientId=\'{D_patientId}\',userFlag = \'{D_userFlag}\' WHERE id = \'{D_id}\'","infoString":"更新数据失败"}', 			
			'{"nodeType":"sql","parameter":"D_id,D_patientId,I_regDate,I_startTime,I_endTime,D_orderIdHIS,M_id,I_customerFamilyId,F_timenow","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_pushtask (parameter,title,content,customerUserId,customerFamilyId,eventType,pushTime,createTime) values(\'{D_id}\',\'预约成功\',\'就诊时间{I_regDate} {I_startTime}到{I_endTime} 。预约流水号：{D_orderIdHIS},病人ID：{D_patientId}。\',\'{M_id}\',\'{I_customerFamilyId}\',\'3\',\'{I_regDate} {I_startTime}:00\',\'{F_timenow}\')","infoString":"查询无数据"}', 
			'{"nodeType":"sql","parameter":"D_id","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT orderId,resultDesc,orderIdHIS,userFlag FROM hz_appointsorder WHERE id=\'{D_id}\'","infoString":"查询无数据"}', 
			), 		
		
		'getDoctorBookingScheduleSyn'   => array(
			#'{"nodeType":"sql","parameter":"I_hospitalId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"select maxRegDays from hz_hospitalinfo where hospitalId=\'{I_hospitalId}\' and hosStatus = \'0\'","infoString":"查找不到相应数据"}',
			'{"nodeType":"xml","parameter":"I_hospitalId,I_deptId,I_doctorId,F_datenow,F_datemore","funcName":"getRegInfo","dataType":"","resultType":"list","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><deptId>{I_deptId}</deptId><doctorId>{I_doctorId}</doctorId><startDate>{F_datenow}</startDate><endDate>{F_datemore}</endDate></req>","replaceXmlData":"hospitalId,deptId,doctorId,regDate,regWeekDay,timeFlag,regTotalCount,regLeaveCount,regFee,treatFee","returnXmlData":"list"}',
			),   
		
		'getGuideList'   => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"xml","parameter":"D_patientId,I_hospitalId","funcName":"getGuideList","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId></req>","replaceXmlData":"","returnXmlData":"one"}' 
			),   
		); 
	
?>
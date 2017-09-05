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
* 
*/
$apiConfig = array(
	     //要过滤的方法
	    'noCheckFun' => array(
			'{"funName":"login|register|registerNew|updatePushTask|checkPhone|registerVerifyCode|checkVerifyCode|resetPwdVerifyCode|checkResetPwdVerifyCode|updateUserPwd|confirmPatient|hospitalList|articlesList|articlesInfo"}'
			), 
		'checkUser' => array(
			'{"nodeType":"data","parameter":"I_timestamp,I_token","resultType":"entity","sqlType":"select","sqlString":"select id,customerFamilyId,timestamp,token from hz_customeruser where timestamp = \'{I_timestamp}\' and token =\'{I_token}\'","infoString":"用户未登录"}'			
			),  
	    'checkToken' => array(
			'{"nodeType":"data","parameter":"I_timestamp,I_token,I_id","resultType":"return","sqlType":"select","sqlString":"select id,timestamp,token from hz_customeruser where timestamp = \'{I_timestamp}\' and token =\'{I_token}\' and id=\'{I_id}\'"}',
			),  
		'checkPhone' => array(
			'{"nodeType":"data","parameter":"I_phone","resultType":"return","sqlType":"select","sqlString":"select phone from hz_customeruser where phone = \'{I_phone}\' ","infoString":"未检查到数据"}',
			),  
		'checkVerifyCode' => array(
			'{"nodeType":"data","parameter":"I_phone,I_code","resultType":"return","sqlType":"select","sqlString":"SELECT id FROM hz_verifysmscode t WHERE t.phone=\'{I_phone}\'  and type=\'0\' AND t.code=\'{I_code}\' AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码未检查到数据"}',
			), 
		'checkResetPwdVerifyCode' => array(
			'{"nodeType":"data","parameter":"I_phone,I_code","resultType":"return","sqlType":"select","sqlString":"SELECT id FROM hz_verifysmscode t WHERE t.phone=\'{I_phone}\'  and type=\'0\' AND t.code=\'{I_code}\' AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码未检查到数据"}',
			),  
		
		'checkHisApi' => array(
			'{"nodeType":"data","parameter":"I_hospitalId","resultType":"entity","sqlType":"select","sqlString":"select hisUrl,appKey from hz_hospitalinfo where hospitalId=\'{I_hospitalId}\' and hosStatus = \'0\'"}'
			),  
		'updateToken' => array(
			'{"nodeType":"data","parameter":"D_timestamp,D_token,D_id","resultType":"boolean","sqlType":"update","sqlString":"update hz_customeruser set  timestamp=\'{D_timestamp}\',token=\'{D_token}\' where id=\'{D_id}\'"}',
			),   
		'updatePushTask' => array(
			'{"nodeType":"data","parameter":"I_id,I_customerUserId,F_timenow","resultType":"exesql","sqlType":"update","sqlString":"update hz_pushtask set pushStatus=\'over\',updateTime=\'{F_timenow}\' where id=\'{I_id}\' and  customerUserId=\'{I_customerUserId}\'"}',
			),   
		'updateUserPhoto' => array(
			'{"nodeType":"data","parameter":"M_id,I_userPhoto","resultType":"return","sqlType":"update","sqlString":"update hz_customeruser set userPhoto=\'{I_userPhoto}\'  where id=\'{M_id}\'"}',
			),   
		'updateUserPwd'    => array(
			'{"nodeType":"data","parameter":"I_phone,I_code","resultType":"entity","sqlType":"select","sqlString":"SELECT phone FROM hz_verifysmscode t WHERE t.phone=\'{I_phone}\' AND t.code=\'{I_code}\' and type=\'1\' AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码未检查到数据"}',
			'{"nodeType":"data","parameter":"D_phone","resultType":"entity","sqlType":"select","sqlString":"select id from hz_customeruser where  phone =\'{D_phone}\'","infoString":"用户名不存在"}',
			'{"nodeType":"data","parameter":"D_id,I_password,I_code,","resultType":"return","sqlType":"update","sqlString":"update hz_customeruser set PASSWORD=MD5(CONCAT(MD5(\'{I_password}\'),\'{I_code}\')),ENCRYPT=\'{I_code}\' where id=\'{D_id}\'","infoString":"更新失败"}',
			), 
		'updateUserResetPwd'    => array(  
			'{"nodeType":"data","parameter":"M_id","resultType":"entity","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where  id =\'{M_id}\'","infoString":"用户名不存在"}',
			'{"nodeType":"data","parameter":"M_id,I_oldPassword,D_encrypt","resultType":"entity","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where  id =\'{M_id}\' and PASSWORD=MD5(CONCAT(MD5(\'{I_oldPassword}\'),\'{D_encrypt}\'))","infoString":"旧密码不正确"}',
			'{"nodeType":"data","parameter":"M_id,I_password,","resultType":"return","sqlType":"update","sqlString":"update hz_customeruser set PASSWORD=MD5(CONCAT(MD5(\'{I_password}\'),\'123456\')),ENCRYPT=\'123456\' where id=\'{M_id}\'","infoString":"修改密码失败"}',
			), 
		'registerold'    => array(
			'{"nodeType":"data","parameter":"I_loginName,I_code","resultType":"return","sqlType":"select","sqlString":"SELECT id FROM hz_verifysmscode t WHERE t.phone=\'{I_loginName}\' AND t.code=\'{I_code}\'  and type=\'0\'  AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码未检查到数据"}',
			'{"nodeType":"pass","parameter":"I_loginName","resultType":"boolean","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where   phone = \'{I_loginName}\'","infoString":"用户名已存在"}',
			'{"nodeType":"data","parameter":"I_loginName,I_password,I_code,F_ip,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customeruser(userName,phone,PASSWORD,ENCRYPT,regIp,regTime,timestamp,token) VALUES(\'{I_loginName}\',\'{I_loginName}\',MD5(CONCAT(MD5(\'{I_password}\'),\'{I_code}\')),\'{I_code}\',\'{F_ip}\',\'{F_timenow}\',UNIX_TIMESTAMP(),MD5(CONCAT(\'{I_loginName}\',MD5(UNIX_TIMESTAMP()))));","infoString":"用户名注册失败"}',
			'{"nodeType":"data","parameter":"D_autoid,I_loginName,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customerfamily (customerUserId,phone,ownership,status,createDate) VALUES (\'{D_autoid}\',\'{I_loginName}\',\'本人\',\'0\',\'{F_timenow}\')","infoString":"添加数据失败"}',
			'{"nodeType":"data","parameter":"D_autoid","resultType":"entity","sqlType":"select","sqlString":"SELECT id,customerUserId FROM hz_customerfamily  WHERE  id=\'{D_autoid}\' ","infoString":"查找不到相应数据"}',			
			'{"nodeType":"data","parameter":"D_customerUserId,D_id","resultType":"return","sqlType":"update","sqlString":"update hz_customeruser set customerfamilyId=\'{D_id}\' where id=\'{D_customerUserId}\'","infoString":"注册成功未绑定用户"}'
			), 
		'login'    => array(
			'{"nodeType":"data","parameter":"I_loginName","resultType":"entity","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where  phone = \'{I_loginName}\'","infoString":"手机号码不正确"}',
			'{"nodeType":"data","parameter":"D_id,D_encrypt,I_password","resultType":"entity","sqlType":"select","sqlString":"select id,loginNum from hz_customeruser where  id =\'{D_id}\' and password=MD5(CONCAT(MD5(\'{I_password}\'),\'{D_encrypt}\'))","infoString":"密码不正确"}', 
			'{"nodeType":"data","parameter":"I_uuid","resultType":"exesql","sqlType":"update","sqlString":"update hz_customeruser set uuid=\'\',devType=\'\' where uuid=MD5(\'{I_uuid}\')","infoString":"更新失败"}',
			'{"nodeType":"data","parameter":"D_id,D_loginNum,F_ip,F_timenow,I_uuid,I_devType","resultType":"boolean","sqlType":"update","sqlString":"update hz_customeruser set lastIp=\'{F_ip}\',lastTime=\'{F_timenow}\',loginNum=({D_loginNum}+1),timestamp=UNIX_TIMESTAMP(),token=CONCAT(\'{D_id}\',MD5(UNIX_TIMESTAMP())),uuid=MD5(\'{I_uuid}\'),devType=\'{I_devType}\' where id=\'{D_id}\'","infoString":"更新失败"}',
			'{"nodeType":"data","parameter":"D_id","resultType":"entity","sqlType":"select","sqlString":"select userName,trueName,phone,customerFamilyId,timestamp,token,userPhoto from hz_customeruser where id=\'{D_id}\'","infoString":""}',
			),
		'logout' => array(
			'{"nodeType":"data","parameter":"I_timestamp,I_token","resultType":"boolean","sqlType":"update","sqlString":"update hz_customeruser set  timestamp=\'\',token=\'\',uuid=\'\',devType=\'\' where timestamp=\'{I_timestamp}\' and token=\'{I_token}\'"}',
			), 
		'hospitalList'    => array(
			'{"nodeType":"data","parameter":"","resultType":"list","sqlType":"select","sqlString":"select hospitalId,hospitalName,hospitalLevel,hospitalPhoto,favoriteNum,commentNum  from hz_hospitalinfo where hosStatus =\'0\'","infoString":"查找不到相应数据"}'
			), 
		'hospitalInfo'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId","resultType":"list","sqlType":"select","sqlString":"select hospitalId,hospitalName,hospitalLevel,hospitalPhoto,info,favoriteNum,commentNum  from hz_hospitalinfo where hospitalId=\'{I_hospitalId}\' and hosStatus = \'0\'","infoString":"查找不到相应数据"}'
			), 
		'searchHospital'    => array(
			'{"nodeType":"data","parameter":"I_hospitalName","resultType":"list","sqlType":"select","sqlString":"select hospitalId,hospitalName,hospitalLevel,hospitalPhoto,favoriteNum,commentNum  from hz_hospitalinfo where hospitalName like \'%{I_hospitalName}%\' and hosStatus = \'0\'","infoString":"查找不到相应数据"}'
			), 
		'deptList'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId","resultType":"list","sqlType":"select","sqlString":" SELECT hospitalId,deptId,deptName,parentId,visitAddress,orderId FROM hz_deptinfo WHERE  hospitalId=\'{I_hospitalId}\' order by parentId,orderId ","infoString":"查找不到相应数据"}'
			),  
		'deptInfo'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId,I_deptId","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,d.deptName,d.info,d.favoriteNum,d.commentNum,d.deptLevel,d.deptPhoto,d.visitAddress FROM hz_deptinfo d,hz_hospitalinfo h WHERE d.hospitalId=h.hospitalId and d.hospitalId=\'{I_hospitalId}\' and d.deptId=\'{I_deptId}\' and h.hosStatus = \'0\'","infoString":"查找不到相应数据"}'
			),  
		'searchDept'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId,I_deptName","resultType":"list","sqlType":"select","sqlString":"SELECT hospitalId,deptId,deptName,visitAddress FROM hz_deptinfo WHERE  hospitalId=\'{I_hospitalId}\'  and  deptName like \'%{I_deptName}%\' and parentId<>\'-1\'","infoString":"查找不到相应数据"}'
			), 
		'doctorList'    => array(
			'{"nodeType":"if","parameter":"I_hospitalId,IF|I_deptId","resultType":"rows","sqlType":"select","sqlString":"SELECT count(1) as rows FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and d.hospitalId=\'{I_hospitalId}\' <if> and d.deptId=\'{I_deptId}\'</if> and d.status=\'0\'  and h.hosStatus = \'0\'","infoString":"查找不到相应数据"}',
			'{"nodeType":"if","parameter":"I_hospitalId,IF|I_deptId,I_rowed,I_pageSize","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and d.hospitalId=\'{I_hospitalId}\' <if> and d.deptId=\'{I_deptId}\'</if>   and d.status=\'0\'  and h.hosStatus = \'0\' limit {I_rowed},{I_pageSize} ","infoString":"查找不到相应数据"}'
			), 
		'doctorInfo'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId,I_deptId,I_doctorId","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum,d.info FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId  and  d.hospitalId=\'{I_hospitalId}\' and d.deptId=\'{I_deptId}\' and d.doctorId=\'{I_doctorId}\'  and d.status=\'0\'  and h.hosStatus = \'0\'","infoString":"查找不到相应数据"}'
			), 
		'searchDoctor'    => array(
			'{"nodeType":"data","parameter":"I_doctorName","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and d.doctorName like \'%{I_doctorName}%\'  and d.status=\'0\'  and h.hosStatus = \'0\'","infoString":"查找不到相应数据"}'
			), 
		'searchDoctorToHospital'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId,I_doctorName","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and  d.hospitalId=\'{I_hospitalId}\' and h.hosStatus = \'0\' and d.doctorName  like \'%{I_doctorName}%\'","infoString":"查找不到相应数据"}'
			),
		'searchDoctorToDept'    => array(
			'{"nodeType":"data","parameter":"I_hospitalId,I_deptId,I_doctorName","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE  d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId AND d.deptId = p.deptId and  d.hospitalId=\'{I_hospitalId}\' and d.deptId=\'{I_deptId}\'  and h.hosStatus = \'0\' and d.doctorName like \'%{I_doctorName}%\'","infoString":"查找不到相应数据"}'
			),  
		'regList'   => array(
			'{"nodeType":"if","parameter":"I_hospitalId,I_deptId,IF|I_doctorId,I_regDate","resultType":"list","sqlType":"select","sqlString":"SELECT r.hospitalId,r.deptId,r.doctorId,r.doctorName,d.title,r.regDate,r.regFee,r.treatFee,(r.regFee+r.treatFee)/100 AS allFee,r.timeFlag,CASE WHEN r.timeFlag = \'1\' THEN \'上午\' WHEN r.timeFlag = \'2\' THEN \'下午\' END AS timeDesc ,r.regLeaveCount,d.title,d.doctorPhoto,d.favoriteNum,d.commentNum FROM hz_reginfo r,hz_doctorinfo d WHERE r.hospitalId=d.hospitalId AND r.deptId=d.deptId AND r.doctorId=d.doctorId AND regStatus=1 and d.hospitalId=\'{I_hospitalId}\'  and d.deptId=\'{I_deptId}\' <if>and d.doctorId=\'{I_doctorId}\'</if> AND r.regDate=\'{I_regDate}\' ORDER BY  d.doctorId, r.timeFlag","infoString":"查找不到相应数据"}'
			), 
		'getDoctorBookingSchedule'   => array(
			'{"nodeType":"data","parameter":"I_hospitalId","resultType":"entity","sqlType":"select","sqlString":"select maxRegDays from hz_hospitalinfo where hospitalId=\'{I_hospitalId}\' and hosStatus = \'0\'","infoString":"查找不到相应数据"}',
			'{"nodeType":"data","parameter":"I_hospitalId,I_deptId,I_doctorId,D_maxRegDays","resultType":"list","sqlType":"select","sqlString":"SELECT  t.hospitalId,t.deptId,t.doctorId,t.regDate,t.regWeekDay,t.timeFlag,t.regTotalCount,t.regLeaveCount,t.regFee,t.treatFee FROM hz_reginfo t WHERE t.hospitalId=\'{I_hospitalId}\' AND t.deptId=\'{I_deptId}\' AND t.doctorId=\'{I_doctorId}\' AND t.regDate>=CURDATE() AND t.regDate<=DATE_ADD(CURDATE(), INTERVAL {D_maxRegDays} DAY) GROUP BY t.hospitalId,t.deptId,t.doctorId,t.regDate,t.regWeekDay,t.timeFlag,t.regTotalCount,t.regleaveCount,t.regFee,t.treatFee ORDER BY t.regDate,t.timeFlag","infoString":"查找不到相应数据"}'
			),   
		'medicineScheduleGroupsUpdate' => array(
			'{"nodeType":"data","parameter":"M_id,I_id","resultType":"entity","sqlType":"select","sqlString":"SELECT g.id FROM hz_medicine m,hz_medicineitem i ,hz_medicineitemschedulegroup g WHERE m.id=i.medicineId AND i.id =g.medicineItemId  AND m.customerUserId=\'{M_id}\' AND g.id=\'{I_id}\' ","infoString":"查找不到相应数据"}',
			'{"nodeType":"data","parameter":"D_id,I_startDate,I_consumptionHoursString,I_quantityString,I_days","resultType":"return","sqlType":"update","sqlString":"UPDATE hz_medicineitemschedulegroup SET startDate = \'{I_startDate}\',consumptionHoursString = \'{I_consumptionHoursString}\',quantityString = \'{I_quantityString}\',days = \'{I_days}\' WHERE id = \'{D_id}\' ","infoString":"更新失败"}',
			),       
		'medicineScheduleGroups'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId,I_dateTime","resultType":"list","sqlType":"select","sqlString":"SELECT g.id,i.drugName,g.startDate,g.consumptionHoursString,g.quantityString,g.doseType,g.everyXDays,g.daysToTake,g.days,g.status  FROM hz_medicine m,hz_medicineitem i ,hz_medicineitemschedulegroup g WHERE m.id=i.medicineId AND i.id =g.medicineItemId AND  m.customerFamilyId=\'{I_customerFamilyId}\' AND m.customerUserId=\'{M_id}\'  AND DATE_ADD(g.startDate, INTERVAL g.days-1 DAY)>= DATE(\'{I_dateTime}\')","infoString":"查找不到相应数据"}'
			),
		'medicineScheduleGroupsById'   => array(
			'{"nodeType":"data","parameter":"M_id,I_id,I_dateTime","resultType":"list","sqlType":"select","sqlString":"SELECT g.id,i.drugName,g.startDate,g.consumptionHoursString,g.quantityString,g.doseType,g.everyXDays,g.daysToTake,g.days,g.status  FROM hz_medicine m,hz_medicineitem i ,hz_medicineitemschedulegroup g WHERE m.id=i.medicineId AND i.id =g.medicineItemId AND  g.id=\'{I_id}\' AND m.customerUserId=\'{M_id}\'  AND DATE_ADD(g.startDate, INTERVAL g.days-1 DAY)>= DATE(\'{I_dateTime}\')","infoString":"查找不到相应数据"}'
			),  
		'medicineScheduleUpdate' => array(
			'{"nodeType":"data","parameter":"M_id,I_id","resultType":"entity","sqlType":"select","sqlString":"SELECT s.id FROM hz_medicine m,hz_medicineitem i ,hz_medicineschedule s WHERE m.id=i.medicineId AND i.id =s.medicineItemId AND m.customerUserId=\'{M_id}\'  AND s.id = \'{I_id}\' ","infoString":"查找不到相应数据"}',
			'{"nodeType":"data","parameter":"D_id,F_timenow,I_status","resultType":"return","sqlType":"update","sqlString":"UPDATE  hz_medicineschedule SET actualDateTime = \'{F_timenow}\',STATUS = \'{I_status}\' WHERE id = \'{D_id}\' ","infoString":"更新失败"}',
			),    
		'medicineSchedule'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId,I_dateTime","resultType":"list","sqlType":"select","sqlString":"SELECT s.id,i.drugName,s.originalDateTime,s.actualDateTime,s.consumptionHours,s.quantity,s.doseType,s.status FROM hz_medicine m,hz_medicineitem i ,hz_medicineschedule s WHERE m.id=i.medicineId AND i.id =s.medicineItemId  AND m.customerFamilyId=\'{I_customerFamilyId}\' AND m.customerUserId=\'{M_id}\'  AND DATE(s.originalDateTime) =  DATE(\'{I_dateTime}\')","infoString":"查找不到相应数据"}'
			),
		'medicineList'   => array(
			'{"nodeType":"data","parameter":"M_id,M_customerFamilyId","resultType":"list","sqlType":"select","sqlString":"SELECT m.id,m.hospitalId,h.hospitalName,m.deptId,d.deptName,m.doctorId,m.doctorName,m.diagnos,m.notes,m.medicineDate FROM hz_medicine m,hz_hospitalinfo h,hz_deptinfo d WHERE m.hospitalId = h.hospitalId AND m.hospitalId = d.hospitalId AND m.deptId = d.deptId AND m.customerFamilyId=\'{M_customerFamilyId}\' AND m.customerUserId=\'{M_id}\'  ORDER BY m.medicineDate desc","infoString":"查找不到相应数据"}'
			),
		'medicineListToC'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId","resultType":"list","sqlType":"select","sqlString":"SELECT m.id,m.hospitalId,h.hospitalName,m.deptId,d.deptName,m.doctorId,m.doctorName,m.diagnos,m.notes,m.medicineDate FROM hz_medicine m,hz_hospitalinfo h,hz_deptinfo d WHERE m.hospitalId = h.hospitalId AND m.hospitalId = d.hospitalId AND m.deptId = d.deptId AND m.customerFamilyId=\'{I_customerFamilyId}\' AND m.customerUserId=\'{M_id}\'  ORDER BY m.medicineDate desc","infoString":"查找不到相应数据"}'
			),  
		
		'favoriteHospital'    => array(
			'{"nodeType":"verifi","run":"pass","parameter":"M_id,I_hospitalId","resultType":"entity","sqlType":"select","sqlString":"select id from  hz_favoritehospital where customerUserId =\'{M_id}\' and hospitalId= \'{I_hospitalId}\'","infoString":""}',
			'{"nodeType":"verifi","run":"true","parameter":"D_id,I_status,F_timenow","resultType":"return","sqlType":"update","sqlString":"UPDATE hz_favoritehospital SET STATUS = \'{I_status}\',createTime=\'{F_timenow}\'  WHERE id = \'{D_id}\'","infoString":"操作失败"}',
			'{"nodeType":"verifi","run":"false","parameter":"M_id,I_hospitalId,F_timenow,I_status","resultType":"return","sqlType":"insert","sqlString":"insert into hz_favoritehospital (customerUserId,hospitalId,createTime,STATUS) VALUES(\'{M_id}\',\'{I_hospitalId}\',\'{F_timenow}\',\'{I_status}\')","infoString":"操作失败"}',
			), 
		'favoriteHospitalList'   => array(
			'{"nodeType":"data","parameter":"M_id","resultType":"list","sqlType":"select","sqlString":"SELECT hospitalId,hospitalName,hospitalLevel,hospitalPhoto,favoriteNum,commentNum,1 as status FROM hz_hospitalinfo WHERE   hosStatus = \'0\' and hospitalId IN (SELECT hospitalId FROM hz_favoritehospital  WHERE customerUserId =\'{M_id}\' and STATUS=\'1\') UNION SELECT hospitalId,hospitalName,hospitalLevel,hospitalPhoto,favoriteNum,commentNum,0 as status FROM hz_hospitalinfo WHERE hosStatus = \'0\' and hospitalId NOT IN (SELECT hospitalId FROM hz_favoritehospital  WHERE customerUserId =\'{M_id}\' and STATUS=\'1\') ","infoString":"查找不到相应数据"}'
			),  
		'favoritedHospitalList'   => array(
			'{"nodeType":"data","parameter":"M_id","resultType":"list","sqlType":"select","sqlString":"SELECT hospitalId,hospitalName,hospitalLevel,hospitalPhoto,favoriteNum,commentNum,1 as status FROM hz_hospitalinfo WHERE hosStatus = \'0\' and  hospitalId IN (SELECT hospitalId FROM hz_favoritehospital  WHERE customerUserId =\'{M_id}\' and STATUS=\'1\')","infoString":"查找不到相应数据"}'			
			),  
		'favoriteDoctor'    => array(
			'{"nodeType":"verifi","run":"pass","parameter":"M_id,I_hospitalId,I_deptId,I_doctorId","resultType":"entity","sqlType":"select","sqlString":"select id from  hz_favoritedoctor where customerUserId =\'{M_id}\' and hospitalId= \'{I_hospitalId}\' and deptId=\'{I_deptId}\' and doctorId= \'{I_doctorId}\'","infoString":""}',
			'{"nodeType":"verifi","run":"true","parameter":"D_id,I_status,F_timenow","resultType":"return","sqlType":"update","sqlString":"UPDATE hz_favoritedoctor SET STATUS = \'{I_status}\',createTime=\'{F_timenow}\'  WHERE id = \'{D_id}\'","infoString":"操作失败"}',
			'{"nodeType":"verifi","run":"false","parameter":"M_id,I_hospitalId,I_deptId,I_doctorId,F_timenow,I_status","resultType":"return","sqlType":"insert","sqlString":"insert into hz_favoritedoctor (customerUserId,hospitalId,deptId,doctorId,createTime,STATUS) VALUES(\'{M_id}\',\'{I_hospitalId}\',\'{I_deptId}\',\'{I_doctorId}\',\'{F_timenow}\',\'{I_status}\')","infoString":"操作失败"}',
			),
		'favoriteDoctorList'   => array( 
			'{"nodeType":"data","parameter":"M_id,I_hospitalId,I_deptId","resultType":"list","sqlType":"select","sqlString":"SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum,d.info,1 as status  FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p,hz_favoritedoctor f WHERE d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.hospitalId=f.hospitalId AND d.deptId=f.deptId AND  d.deptId=p.deptId AND d.doctorId=f.doctorId  and h.hosStatus = \'0\' AND  f.hospitalId=\'{I_hospitalId}\' AND f.deptId=\'{I_deptId}\' AND f.customerUserId =\'{M_id}\' and f.status=\'1\'  UNION SELECT d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum,d.info,0 AS STATUS  FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p WHERE d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.deptId=p.deptId  and  d.hospitalId=\'{I_hospitalId}\' and d.deptId=\'{I_deptId}\' and d.doctorId NOT IN (SELECT doctorId FROM hz_favoritedoctor  WHERE hospitalId=\'{I_hospitalId}\' AND deptId=\'{I_deptId}\' AND customerUserId =\'{M_id}\' and status=\'1\')","infoString":"查找不到相应数据"}', 
			),  
		'favoritedDoctorList'   => array( 
			'{"nodeType":"data","parameter":"M_id","resultType":"list","sqlType":"select","sqlString":"SELECT  d.hospitalId,h.hospitalName,d.deptId,p.deptName, d.doctorId,d.doctorName,d.title,d.doctorPhoto,d.favoriteNum ,d.commentNum,d.info,1 as status  FROM hz_doctorinfo d,hz_hospitalinfo h,hz_deptinfo p,hz_favoritedoctor f WHERE d.hospitalId=h.hospitalId AND d.hospitalId=p.hospitalId AND d.hospitalId=f.hospitalId AND d.deptId=f.deptId AND  d.deptId=p.deptId  AND d.doctorId=f.doctorId  and h.hosStatus = \'0\' AND f.customerUserId =\'{M_id}\' and f.status=\'1\'","infoString":"查找不到相应数据"}', 
			),  
		'customerFamilyList'   => array(
			'{"nodeType":"data","parameter":"M_id","resultType":"list","sqlType":"select","sqlString":"SELECT id,customerUserId,trueName,phone,email,idNo,birthDay,tel,address,sex,nation,province,city,area,ownership,status FROM hz_customerfamily  WHERE customerUserId =\'{M_id}\' ORDER BY STATUS ","infoString":"查找不到相应数据"}'
			),   
		'customerFamilyInfo'   => array(
			'{"nodeType":"data","parameter":"I_id,M_id","resultType":"list","sqlType":"select","sqlString":"SELECT id,customerUserId,trueName,phone,email,idNo,birthDay,tel,address,sex,nation,province,city,area,ownership,status FROM hz_customerfamily  WHERE customerUserId =\'{M_id}\' and id=\'{I_id}\' ","infoString":"查找不到相应数据"}'
			), 
		//验证，本人是不能添加与修改的要做验证
		'customerFamilyAdd'   => array(
			'{"nodeType":"data","parameter":"M_id,I_trueName,I_phone,I_email,I_idNo,I_birthDay,I_tel,I_address,I_sex,I_nation,I_province,I_city,I_area,I_ownership,F_timenow","resultType":"return","sqlType":"insert","sqlString":"insert into hz_customerfamily (customerUserId,trueName,phone,email,idNo,birthDay,tel,address,sex,nation,province,city,area,ownership,createDate) VALUES (\'{M_id}\',\'{I_trueName}\',\'{I_phone}\',\'{I_email}\',\'{I_idNo}\',\'{I_birthDay}\',\'{I_tel}\',\'{I_address}\',\'{I_sex}\',\'{I_nation}\',\'{I_province}\',\'{I_city}\',\'{I_area}\',\'{I_ownership}\',\'{F_timenow}\')","infoString":"添加数据失败"}'
			), 
		'customerFamilyEdit'   => array(
			'{"nodeType":"data","parameter":"I_id,M_id,I_phone,I_email,I_birthDay,I_tel,I_address,I_sex,I_nation,I_province,I_city,I_area,I_ownership","resultType":"return","sqlType":"update","sqlString":"update hz_customerfamily set phone=\'{I_phone}\',email=\'{I_email}\',birthDay=\'{I_birthDay}\',tel=\'{I_tel}\',address=\'{I_address}\',sex=\'{I_sex}\',nation=\'{I_nation}\',province=\'{I_province}\',city=\'{I_city}\',area=\'{I_area}\',ownership=\'{I_ownership}\' where id=\'{I_id}\' and customerUserId=\'{M_id}\'","infoString":"更新数据失败"}'
			),    
		'customerFamilySelfEdit'   => array(
			'{"nodeType":"data","parameter":"I_id,M_id,I_phone,I_email,I_birthDay,I_tel,I_address,I_sex,I_nation,I_province,I_city,I_area,I_ownership","resultType":"return","sqlType":"update","sqlString":"update hz_customerfamily set phone=\'{I_phone}\',email=\'{I_email}\',birthDay=\'{I_birthDay}\',tel=\'{I_tel}\',address=\'{I_address}\',sex=\'{I_sex}\',nation=\'{I_nation}\',province=\'{I_province}\',city=\'{I_city}\',area=\'{I_area}\',ownership=\'{I_ownership}\' where id=\'{I_id}\' and customerUserId=\'{M_id}\'","infoString":"更新数据失败"}',
			'{"nodeType":"data","parameter":"M_id,I_phone,I_email,I_birthDay,I_tel,I_address,I_sex,I_nation,I_province,I_city,I_area","resultType":"return","sqlType":"update","sqlString":"update hz_customeruser set phone=\'{I_phone}\',email=\'{I_email}\',birthDay=\'{I_birthDay}\',tel=\'{I_tel}\',address=\'{I_address}\',sex=\'{I_sex}\',nation=\'{I_nation}\',province=\'{I_province}\',city=\'{I_city}\',area=\'{I_area}\' where id=\'{M_id}\'","infoString":"更新数据失败"}'
			),   
		'customerCardList'   => array(
			'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,I_hospitalId,IF|I_cardType","resultType":"list","sqlType":"select","sqlString":"SELECT t.id,t.customerFamilyId,t.hospitalId,t.cardType,t.cardId,t.patientId FROM hz_customercard t,hz_customerfamily f WHERE t.customerFamilyId=f.id AND f.customerUserId =\'{M_id}\' <if>and t.cardType=\'{I_cardType}\'</if>  AND t.customerFamilyId=\'{I_customerFamilyId}\' AND t.hospitalId=\'{I_hospitalId}\'","infoString":"查找不到相应数据"}'
			),   
		'customerCardHospitalList'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId","resultType":"list","sqlType":"select","sqlString":"SELECT h.hospitalId,h.hospitalName,t.customerFamilyId,t.hospitalId,t.cardType,t.cardId,t.patientId FROM hz_customercard t,hz_customerfamily f,hz_hospitalinfo h WHERE t.customerFamilyId=f.id AND t.hospitalId=h.hospitalId AND f.customerUserId =\'{M_id}\' AND t.customerFamilyId=\'{I_customerFamilyId}\'","infoString":"查找不到相应数据"}'
			),   
		'customerAllCardHospitalList'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId","resultType":"list","sqlType":"select","sqlString":"SELECT h.hospitalId,h.hospitalName,t.customerFamilyId,t.hospitalId,t.cardType,t.cardId,t.patientId,0 AS STATUS FROM hz_customercard t,hz_customerfamily f,hz_hospitalinfo h WHERE t.customerFamilyId=f.id AND t.hospitalId=h.hospitalId AND f.customerUserId =\'{M_id}\' AND t.customerFamilyId=\'{I_customerFamilyId}\' UNION  SELECT h.hospitalId,h.hospitalName, \'{M_id}\' AS customerFamilyId,h.hospitalId,NULL AS cardType ,NULL AS cardId ,NULL AS patientId,1 AS STATUS FROM  hz_hospitalinfo h WHERE h.hospitalId NOT IN (SELECT hospitalId FROM hz_customercard WHERE customerFamilyId=\'{{M_id}}\')","infoString":"查找不到相应数据"}'
			),   
		
		'customerCardInfo'   => array(
			'{"nodeType":"data","parameter":"I_id,M_id,I_customerFamilyId,I_hospitalId","resultType":"list","sqlType":"select","sqlString":"SELECT t.id,t.customerFamilyId,t.hospitalId,t.cardType,t.cardId,t.patientId FROM hz_customercard t,hz_customerfamily f WHERE t.customerFamilyId=f.id AND f.customerUserId =\'{M_id}\' AND t.customerFamilyId=\'{I_customerFamilyId}\' AND t.hospitalId=\'{I_hospitalId}\'  and t.id=\'{I_id}\' ","infoString":"查找不到相应数据"}'
			),  
		'customerCardCheck'   => array(
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"return","sqlType":"select","sqlString":"SELECT t.id,t.cardType,t.cardId,t.patientId FROM hz_customercard t,hz_customerfamily f WHERE t.customerFamilyId=f.id AND f.customerUserId =\'{M_id}\' AND t.customerFamilyId=\'{I_customerFamilyId}\' AND t.hospitalId=\'{I_hospitalId}\' ","infoString":"查找不到相应数据"}'
			),  
		
		'historyList'  => array( 
			'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,IF|I_hospitalId","resultType":"rows","sqlType":"select","sqlString":"SELECT  count(1) as rows  FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND  a.resultCode=\'0\' and  a.customerFamilyId =\'{I_customerFamilyId}\' AND a.customerUserId=\'{M_id}\' <if>and a.hospitalId=\'{I_hospitalId}\'</if>  ORDER BY a.regDate desc","infoString":"查找不到相应数据"}', 
			'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,IF|I_hospitalId,I_rowed,I_pageSize","resultType":"list","sqlType":"select","sqlString":"SELECT h.hospitalName,a.hospitalId,p.deptName,d.doctorName,d.title,d.doctorphoto,a.regDate,a.startTime,a.endTime,a.orderId,a.orderIdHIS,a.clinicCode,a.customerFamilyId,a.fee,a.treatfee,a.resultCode,a.orderIdHIS,a.patientId,a.userFlag,a.payFlag,a.cancelFlag,a.infoFlag,a.returnFlag FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND  a.resultCode=\'0\' and  a.customerFamilyId =\'{I_customerFamilyId}\' AND a.customerUserId=\'{M_id}\' <if>and a.hospitalId=\'{I_hospitalId}\'</if>  ORDER BY  a.regDate desc limit {I_rowed},{I_pageSize} ","infoString":"查找不到相应数据"}', 
			//'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,IF|I_hospitalId","resultType":"list","sqlType":"select","sqlString":"SELECT h.hospitalName,a.hospitalId,p.deptName,d.doctorName,d.title,d.doctorphoto,a.regDate,a.startTime,a.endTime,a.orderId,a.orderIdHIS,a.customerFamilyId,a.fee,a.treatfee,a.resultCode,a.orderIdHIS,a.userFlag,a.payFlag,a.cancelFlag,a.infoFlag,a.returnFlag FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND  a.resultCode=\'0\' and  a.customerFamilyId =\'{I_customerFamilyId}\' AND a.customerUserId=\'{M_id}\' <if>and a.hospitalId=\'{I_hospitalId}\'</if>  ORDER BY a.returnFlag desc,a.regDate desc","infoString":"查找不到相应数据"}', 
			),  
		
		'historyListForPay'  => array( 
			'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,IF|I_hospitalId","resultType":"rows","sqlType":"select","sqlString":"SELECT  count(1) as rows  FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND  a.resultCode=\'0\'  and a.cancelFlag=\'1\' and a.returnFlag=\'1\' and a.customerFamilyId =\'{I_customerFamilyId}\' AND a.customerUserId=\'{M_id}\' <if>and a.hospitalId=\'{I_hospitalId}\'</if>  ORDER BY  a.regDate desc","infoString":"查找不到相应数据"}', 
			'{"nodeType":"if","parameter":"M_id,I_customerFamilyId,IF|I_hospitalId,I_rowed,I_pageSize","resultType":"list","sqlType":"select","sqlString":"SELECT h.hospitalName,a.hospitalId,p.deptName,d.doctorName,d.title,d.doctorphoto,a.regDate,a.startTime,a.endTime,a.orderId,a.orderIdHIS,a.clinicCode,a.customerFamilyId,a.fee,a.treatfee,a.resultCode,a.orderIdHIS,a.patientId,a.clinicCode,a.userFlag,a.payFlag,a.cancelFlag,a.infoFlag,a.returnFlag FROM hz_appointsorder a,hz_hospitalinfo h,hz_deptinfo p,hz_doctorinfo d WHERE a.hospitalId=h.hospitalId AND a.deptId=p.deptId AND a.doctorId=d.doctorId AND h.hospitalId=p.hospitalId AND p.deptId=d.deptId AND  a.resultCode=\'0\'  and a.cancelFlag=\'1\' and a.returnFlag=\'1\'  and  a.customerFamilyId =\'{I_customerFamilyId}\' AND a.customerUserId=\'{M_id}\' <if>and a.hospitalId=\'{I_hospitalId}\'</if>  ORDER BY  a.regDate desc limit {I_rowed},{I_pageSize} ","infoString":"查找不到相应数据"}', 
			),  
		
		'messageCenterList'  => array( 
			'{"nodeType":"data","parameter":"M_id,I_customerFamilyId,I_eventType","resultType":"list","sqlType":"select","sqlString":"SELECT * FROM hz_pushtask  WHERE customerUserId =\'{M_id}\' and customerFamilyId=\'{I_customerFamilyId}\' and eventType=\'{I_eventType}\' AND DATE(pushTime)=DATE(SYSDATE())","infoString":"查找不到相应数据"}', 
			),  
		'messageCenterById'  => array( 
			'{"nodeType":"data","parameter":"M_id,I_id","resultType":"list","sqlType":"select","sqlString":"SELECT * FROM hz_pushtask  WHERE customerUserId =\'{M_id}\' and id=\'{I_id}\' AND DATE(pushTime)=DATE(SYSDATE())","infoString":"查找不到相应数据"}', 
			),  
		
		'articlesInfo'  => array( 
			'{"nodeType":"data","parameter":"I_id","resultType":"list","sqlType":"select","sqlString":"SELECT id,title,shortTitle,color,author,litpic,content,description,publishTime,updateTime,click,flag,jumpurl FROM hz_articles where id=\'{I_id}\'","infoString":"查找不到相应数据"}', 
			),  
		
		'articlesList'  => array( 
			'{"nodeType":"data","parameter":"","resultType":"list","sqlType":"select","sqlString":"SELECT id,title,shortTitle,color,author,litpic,description,publishTime FROM hz_articles","infoString":"查找不到相应数据"}', 
			),  
		 
		
		); 
?>
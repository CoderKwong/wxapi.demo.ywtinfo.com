<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/**
* 用于在配置流的列表
* 命名规则 I和D要注册大小写
* 命名规则 I_name  I是通过URL获取内容
* 命名规则 D_name  D是通过上一条数据获取内容 verifi
* 'demo'    => array(
	 '{"nodeType":"data|pass|if|verifi",parameter:"[para1,pa]","resultType":"autoid|entity|boolean|return|list","sqlType":"select|update|insert|delete","sqlString":"select hospitalId,hospitalName,hospLevel,hospPhoto,favoriteNum,commentNum  from hz_hospitalinfo where status =0"}'
   ),
*  nodeType:pass 为验证如果返回结果为空则可以进入下一步
*  nodeType:if  如为if 在parameter里只能有一个为IF|参数  sqlString sql语句里 <if>条件</if> 
* "nodeType":"verifi"  如果节点为verifi 那就执行下面两条语句，一个节点返回true的执行，一个节点返回为false的执行
* 
*/
$hisConfig = array( 
	    'getTimeRegInfo' => array(
			'{"nodeType":"xml","parameter":"I_hospitalId,I_deptId,I_doctorId,I_regDate,I_timeFlag","funcName":"getTimeRegInfo","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><deptId>{I_deptId}</deptId><doctorId>{I_doctorId}</doctorId><regDate>{I_regDate}</regDate><timeFlag>{I_timeFlag}</timeFlag></req>","replaceXmlData":"scheduleCode,startTime,endTime,regTotalCount,regLeaveCount","returnXmlData":"list"}' 
			),   
		
		//返回最近一次挂号日期 
		'getLastClinicDate' => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT c.patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"xml","parameter":"D_patientId,I_hospitalId","funcName":"getLastClinicDate","resultType":"entity","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId></req>","replaceXmlData":"dateTime,clinicCode","returnXmlData":"one"}' 
			),  
		
		'labTestList' => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT c.patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"xml","parameter":"D_patientId,I_hospitalId,I_dateTime,I_clinicCode","funcName":"labTestList","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId><dateTime>{I_dateTime}</dateTime><clinicCode>{I_clinicCode}</clinicCode></req>","replaceXmlData":"testNO,subject,status,requestedDateTime,resultsRptDateTime","returnXmlData":"list"}'
			),   
		
		'labTestInfo' => array(
			'{"nodeType":"xml","parameter":"I_hospitalId,I_testNo","funcName":"labTestInfo","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><testNo>{I_testNo}</testNo></req>","replaceXmlData":"itemNo,reportItemName,abnormalIndicator,result,units,lowerLimit,upperLimit","returnXmlData":"list"}'
			),    
		
		'examList' => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT c.patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"xml","parameter":"D_patientId,I_hospitalId,I_dateTime,I_clinicCode","funcName":"examList","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId><dateTime>{I_dateTime}</dateTime><clinicCode>{I_clinicCode}</clinicCode></req>","replaceXmlData":"examNo,examClass,examSubClass,status,reqDateTime,reportDateTime","returnXmlData":"list"}'
			),    
                  
		'examInfo' => array(
			'{"nodeType":"xml","parameter":"I_hospitalId,I_examNo","funcName":"examInfo","resultType":"list","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><examNo>{I_examNo}</examNo></req>","replaceXmlData":"description,abnormal,imperssion,recommedation,examClass,reportDateTime","returnXmlData":"list"}' 
			),    
		
		
         //取消挂号
		'cancelOrder' => array( 
			'{"nodeType":"sql","parameter":"I_orderId,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,orderIdHis FROM hz_appointsorder t WHERE t.orderId=\'{I_orderId}\' AND t.resultCode=\'0\' AND t.cancelFlag=\'1\' AND t.payFlag=\'1\'  and t.returnFlag=\'1\'","infoString":"查询无数据或该订单已取消"}', 
			'{"nodeType":"sql","parameter":"I_hospitalId,I_orderId,F_timenow,I_cancelReason,M_id,I_customerFamilyId","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_cancelorder(hospitalId,orderId,cancelTime,cancelReason,customerUserId,customerFamilyId) VALUES (\'{I_hospitalId}\',\'{I_orderId}\',\'{F_timenow}\',\'{I_cancelReason}\',\'{M_id}\',\'{I_customerFamilyId}\')","infoString":"添加退号订单失败，请再试"}', 
			'{"nodeType":"sql","parameter":"D_autoid,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,hospitalId,orderId,cancelTime,cancelReason FROM hz_cancelorder WHERE id=\'{D_autoid}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,I_orderId,D_orderIdHis,D_cancelTime,D_cancelReason","funcName":"cancelOrder","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><orderId>{I_orderId}</orderId><orderIdHis>{D_orderIdHis}</orderIdHis><cancelTime>{D_cancelTime}</cancelTime><cancelReason>{D_cancelReason}</cancelReason></req>","replaceXmlData":"resultCode,resultDesc","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"D_id,D_resultCode,D_resultDesc","resultType":"entity","dataType":"add","sqlType":"update","sqlString":"UPDATE hz_cancelorder SET resultCode = \'{D_resultCode}\',resultDesc = \'{D_resultDesc}\' WHERE id = \'{D_id}\'","infoString":"更新数据失败"}', 
			'{"nodeType":"sql","parameter":"I_orderId","resultType":"return","dataType":"","sqlType":"update","sqlString":"UPDATE hz_appointsorder SET cancelFlag = \'0\' WHERE orderId = \'{I_orderId}\'","infoString":"取消预约成功"}', 
			),   
		//添加付费
		'payOrder' => array( 
			'{"nodeType":"sql","parameter":"I_orderId,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,orderIdHis FROM hz_appointsorder t WHERE t.orderId=\'{I_orderId}\' AND t.resultCode=\'0\' AND t.cancelFlag=\'1\' AND t.payFlag=\'1\'  and t.returnFlag=\'1\'","infoString":"查询无数据或该订单已支付"}', 
			'{"nodeType":"sql","parameter":"I_hospitalId,I_orderId,F_orderId,I_payCardNum,I_payAmout,I_payMode,F_timenow,M_id,I_customerFamilyId","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_payorder (hospitalId,orderId,orderIdPAY,payCardNum,payAmout,payMode,payTime,customerUserId,customerFamilyId) VALUES (\'{I_hospitalId}\',\'{I_orderId}\',\'{F_orderId}\',\'{I_payCardNum}\',\'{I_payAmout}\',\'{I_payMode}\',\'{F_timenow}\',\'{M_id}\',\'{I_customerFamilyId}\')","infoString":"添加支付订单失败，请再试"}', 
			'{"nodeType":"sql","parameter":"D_autoid,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT orderIdPAY,payCardNum,payAmout,payMode,payTime FROM hz_payorder WHERE id=\'{D_autoid}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,I_orderId,D_orderIdHis,I_patientId,D_orderIdPAY,D_payCardNum,D_payAmout,D_payMode,D_payTime","funcName":"payOrder","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><orderId>{I_orderId}</orderId><orderIdHis>{D_orderIdHis}</orderIdHis><orderIdPAY>{D_orderIdPAY}</orderIdPAY><payCardNum>{D_payCardNum}</payCardNum><patientId>{I_patientId}</patientId><payAmout>{D_payAmout}</payAmout><payMode>{D_payMode}</payMode><payTime>{D_payTime}</payTime><payRespCode/><payRespDesc/></req>","replaceXmlData":"resultCode,resultDesc,clinicCode","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"D_autoid,D_resultCode,D_resultDesc,D_clinicCode","resultType":"entity","dataType":"add","sqlType":"update","sqlString":"UPDATE hz_payorder SET resultCode = \'{D_resultCode}\',resultDesc = \'{D_resultDesc}\',clinicCode = \'{D_clinicCode}\' WHERE id = \'{D_autoid}\'","infoString":"更新数据失败"}', 
			'{"nodeType":"sql","parameter":"I_orderId,D_clinicCode","resultType":"return","dataType":"","sqlType":"update","sqlString":"UPDATE hz_appointsorder SET payFlag = \'0\',clinicCode = \'{D_clinicCode}\' WHERE orderId = \'{I_orderId}\'","infoString":"支付成功"}', 
			),  
		//退费
		'returnPay' => array( 
			'{"nodeType":"sql","parameter":"I_orderId,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,orderIdHis,clinicCode FROM hz_appointsorder t WHERE t.orderId=\'{I_orderId}\' AND t.resultCode=\'0\' AND t.cancelFlag=\'1\' AND t.payFlag=\'0\' and t.returnFlag=\'1\' ","infoString":"查询无数据或该订单已支付"}', 
			'{"nodeType":"sql","parameter":"I_hospitalId,I_orderId,F_orderId,I_payAmout,I_returnReason,F_timenow,M_id,I_customerFamilyId","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_returnpay (hospitalId,orderId,orderIdPAY,returnFee,returnReason,returnTime,customerUserId,customerFamilyId) VALUES (\'{I_hospitalId}\',\'{I_orderId}\',\'{F_orderId}\',\'{I_payAmout}\',\'{I_returnReason}\',\'{F_timenow}\',\'{M_id}\',\'{I_customerFamilyId}\')","infoString":"添加支付订单失败，请再试"}', 
			'{"nodeType":"sql","parameter":"D_autoid,","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT orderId,orderIdPAY,returnFee,returnReason,returnTime FROM hz_returnpay WHERE id=\'{D_autoid}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,I_orderId,D_orderIdPAY,I_patientId,D_clinicCode,D_returnFee,D_returnTime,D_returnReason","funcName":"returnPay","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><orderId>{I_orderId}</orderId><orderIdPAY>{D_orderIdPAY}</orderIdPAY><patientId>{I_patientId}</patientId><clinicCode>{D_clinicCode}</clinicCode><returnFee>{D_returnFee}</returnFee><returnTime>{D_returnTime}</returnTime><returnReason>{D_returnReason}</returnReason><payRespCode/><payRespDesc/></req>","replaceXmlData":"resultCode,resultDesc","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"D_autoid,D_resultCode,D_resultDesc","resultType":"entity","dataType":"add","sqlType":"update","sqlString":"UPDATE hz_returnpay SET resultCode = \'{D_resultCode}\',resultDesc = \'{D_resultDesc}\' WHERE id = \'{D_autoid}\'","infoString":"更新数据失败"}', 
			'{"nodeType":"sql","parameter":"I_hospitalId,I_orderId,D_orderIdPAY,F_timenow,M_id,I_customerFamilyId","resultType":"autoid","dataType":"add","sqlType":"insert","sqlString":"INSERT INTO hz_cancelorder(hospitalId,orderId,cancelTime,cancelReason,customerUserId,customerFamilyId,resultCode,resultDesc) VALUES (\'{I_hospitalId}\',\'{I_orderId}\',\'{F_timenow}\',\'{D_orderIdPAY}\',\'{M_id}\',\'{I_customerFamilyId}\',\'0\',\'取消预约成功\')","infoString":"添加支付订单失败，请再试"}', 
			'{"nodeType":"sql","parameter":"I_orderId","resultType":"return","dataType":"","sqlType":"update","sqlString":"UPDATE hz_appointsorder SET returnFlag = \'0\',cancelFlag=\'0\' WHERE orderId = \'{I_orderId}\'","infoString":"退费成功"}', 
			), 
		
		'register'    => array(
			'{"nodeType":"sql","parameter":"I_loginName,I_code","resultType":"return","dataType":"","sqlType":"select","sqlString":"SELECT id FROM hz_verifysmscode t WHERE t.phone=\'{I_loginName}\' AND t.code=\'{I_code}\'  and type=\'0\'  AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码输入不正确或验证码过期"}',
			'{"nodeType":"xml","parameter":"I_hospitalId,I_idNo,I_trueName","funcName":"confirmPatient","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{I_idNo}</userIdCard><username>{I_trueName}</username></req>","replaceXmlData":"resultCode,resultDesc","returnXmlData":"one"}',
			'{"nodeType":"sql","parameter":"I_loginName","resultType":"boolean","isPass":"pass","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where   phone = \'{I_loginName}\'","infoString":"用户名已存在"}',
			'{"nodeType":"sql","parameter":"I_loginName,I_password,I_code,F_ip,F_timenow,I_idNo,I_trueName,I_birthDay,I_sex","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customeruser(phone,PASSWORD,ENCRYPT,regIp,regTime,trueName,idNo,birthDay,sex) VALUES(\'{I_loginName}\',MD5(CONCAT(MD5(\'{I_password}\'),\'{I_code}\')),\'{I_code}\',\'{F_ip}\',\'{F_timenow}\',\'{I_trueName}\',\'{I_idNo}\',\'{I_birthDay}\',\'{I_sex}\');","infoString":"用户名注册失败"}',
			'{"nodeType":"sql","parameter":"D_autoid,I_loginName,I_idNo,I_trueName,I_birthDay,I_sex,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customerfamily (customerUserId,phone,idNo,trueName,ownership,status,createDate,birthDay,sex) VALUES (\'{D_autoid}\',\'{I_loginName}\',\'{I_idNo}\',\'{I_trueName}\',\'本人\',\'0\',\'{F_timenow}\',\'{I_birthDay}\',\'{I_sex}\')","infoString":"添加数据失败"}',
			'{"nodeType":"sql","parameter":"D_autoid","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,customerUserId FROM hz_customerfamily  WHERE  id=\'{D_autoid}\' ","infoString":"查找不到相应数据"}',			
			'{"nodeType":"sql","parameter":"D_customerUserId,D_id","resultType":"return","dataType":"add","sqlType":"update","sqlString":"update hz_customeruser set customerfamilyId=\'{D_id}\' where id=\'{D_customerUserId}\'","infoString":"注册成功未绑定用户"}',			
			'{"nodeType":"sql","parameter":"D_id","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  id,trueName,idNo,sex  FROM hz_customerfamily WHERE id=\'{D_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,I_idNo,I_trueName","funcName":"confirmPatient","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{I_idNo}</userIdCard><username>{I_trueName}</username></req>","replaceXmlData":"patientId,cardId","returnXmlData":"one"}',			
			'{"nodeType":"sql","parameter":"D_id,I_hospitalId,D_cardId,D_patientId,F_timenow","resultType":"return","sqlType":"insert","sqlString":"insert into hz_customercard (customerFamilyId,hospitalId,cardType,cardId,patientId,createTime) VALUES (\'{D_id}\',\'{I_hospitalId}\',\'1\',\'{D_cardId}\',\'{D_patientId}\',\'{F_timenow}\')","infoString":"添加数据失败"}'
			), 
		
		
		'registerNew'    => array(
			'{"nodeType":"sql","parameter":"I_loginName,I_code","resultType":"return","dataType":"","sqlType":"select","sqlString":"SELECT id FROM hz_verifysmscode t WHERE t.phone=\'{I_loginName}\' AND t.code=\'{I_code}\'  and type=\'0\'  AND t.createtime>=DATE_SUB(SYSDATE(),INTERVAL 20 MINUTE) ","infoString":"验证码输入不正确或验证码过期"}',
			'{"nodeType":"sql","parameter":"I_loginName","resultType":"boolean","isPass":"pass","sqlType":"select","sqlString":"select id,encrypt from hz_customeruser where   phone = \'{I_loginName}\'","infoString":"用户名已存在"}',
			'{"nodeType":"sql","parameter":"I_loginName,I_password,I_code,F_ip,F_timenow,I_idNo,I_trueName,I_birthDay,I_sex","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customeruser(phone,PASSWORD,ENCRYPT,regIp,regTime,trueName,idNo,birthDay,sex) VALUES(\'{I_loginName}\',MD5(CONCAT(MD5(\'{I_password}\'),\'{I_code}\')),\'{I_code}\',\'{F_ip}\',\'{F_timenow}\',\'{I_trueName}\',\'{I_idNo}\',\'{I_birthDay}\',\'{I_sex}\');","infoString":"用户名注册失败"}',
			'{"nodeType":"sql","parameter":"D_autoid,I_loginName,I_idNo,I_trueName,I_birthDay,I_sex,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"insert into hz_customerfamily (customerUserId,phone,idNo,trueName,ownership,status,createDate,birthDay,sex) VALUES (\'{D_autoid}\',\'{I_loginName}\',\'{I_idNo}\',\'{I_trueName}\',\'本人\',\'0\',\'{F_timenow}\',\'{I_birthDay}\',\'{I_sex}\')","infoString":"添加数据失败"}',
			'{"nodeType":"sql","parameter":"D_autoid","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT id,customerUserId FROM hz_customerfamily  WHERE  id=\'{D_autoid}\' ","infoString":"查找不到相应数据"}',			
			'{"nodeType":"sql","parameter":"D_customerUserId,D_id","resultType":"return","dataType":"add","sqlType":"update","sqlString":"update hz_customeruser set customerfamilyId=\'{D_id}\' where id=\'{D_customerUserId}\'","infoString":"注册成功未绑定用户"}',			
			'{"nodeType":"sql","parameter":"D_id","resultType":"return","dataType":"","sqlType":"select","sqlString":"SELECT  id,trueName,idNo,sex  FROM hz_customerfamily WHERE id=\'{D_id}\'","infoString":"查询无数据"}', 
			), 
		
		'customerCardAdd'   => array(
			'{"nodeType":"sql","parameter":"I_hospitalId,I_customerFamilyId","resultType":"return","isPass":"pass","dataType":"","sqlType":"select","sqlString":"SELECT  id  FROM hz_customercard WHERE customerFamilyId=\'{I_customerFamilyId}\' AND hospitalId=\'{I_hospitalId}\'","infoString":"该医院已绑定数据"}', 
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  trueName,idNo,sex  FROM hz_customerfamily WHERE id=\'{I_customerFamilyId}\' AND customerUserId=\'{M_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,D_idNo,D_trueName,D_sex,I_cardId","funcName":"confirmCard","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{D_idNo}</userIdCard><username>{D_trueName}</username><gender>{D_sex}</gender><userCard>{I_cardId}</userCard></req>","replaceXmlData":"resultCode,resultDesc,patientId","returnXmlData":"one"}',			
			'{"nodeType":"sql","parameter":"I_customerFamilyId,I_hospitalId,I_cardType,I_cardId,D_patientId,F_timenow","resultType":"return","sqlType":"insert","sqlString":"insert into hz_customercard (customerFamilyId,hospitalId,cardType,cardId,patientId,createTime) VALUES (\'{I_customerFamilyId}\',\'{I_hospitalId}\',\'{I_cardType}\',\'{I_cardId}\',\'{D_patientId}\',\'{F_timenow}\')","infoString":"添加数据失败"}'
			), 
		'customerCardEdit'   => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  trueName,idNo,sex  FROM hz_customerfamily WHERE id=\'{I_customerFamilyId}\' AND customerUserId=\'{M_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,D_idNo,D_trueName,D_sex,I_cardId","funcName":"confirmCard","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{D_idNo}</userIdCard><username>{D_trueName}</username><gender>{D_sex}</gender><userCard>{I_cardId}</userCard></req>","replaceXmlData":"resultCode,resultDesc,patientId","returnXmlData":"one"}',	 
			'{"nodeType":"sql","parameter":"I_id,I_customerFamilyId,I_cardType,I_cardId,D_patientId,F_timenow","resultType":"return","sqlType":"update","sqlString":"update hz_customercard set cardType=\'{I_cardType}\',cardId=\'{I_cardId}\',patientId=\'{D_patientId}\',createTime=\'{F_timenow}\' where id=\'{I_id}\' and customerFamilyId=\'{I_customerFamilyId}\'","infoString":"更新数据失败"}'
			), 
		   
		'confirmPatient'   => array(
			'{"nodeType":"xml","parameter":"I_hospitalId,I_idNo,I_trueName","funcName":"confirmPatient","dataType":"","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><userIdCard>{I_idNo}</userIdCard><username>{I_trueName}</username></req>","replaceXmlData":"phone","returnXmlData":"one"}'			
			), 
		
		'cardMoney'   => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  trueName,ownership,id AS customerFamilyId   FROM hz_customerfamily WHERE id=\'{I_customerFamilyId}\' AND customerUserId=\'{M_id}\'","infoString":"查询无数据"}', 
			'{"nodeType":"sql","parameter":"I_hospitalId,I_customerFamilyId","resultType":"entity","dataType":"add","sqlType":"select","sqlString":"SELECT  c.patientId,c.cardId,c.patientId,h.hospitalId,h.hospitalName  FROM hz_customercard c,hz_hospitalinfo h WHERE c.hospitalId=h.hospitalId and c.customerFamilyId=\'{I_customerFamilyId}\' AND c.hospitalId=\'{I_hospitalId}\'","infoString":"查询无数据"}', 
			'{"nodeType":"xml","parameter":"I_hospitalId,D_patientId,D_cardId","funcName":"cardMoney","dataType":"add","resultType":"entity","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\" ?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId><userCard>{D_cardId}</userCard></req>","replaceXmlData":"money","returnXmlData":"one"}',			
			), 
		
		//排除候诊
		'waitingQueue' => array(
			'{"nodeType":"sql","parameter":"M_id,I_customerFamilyId,I_hospitalId","resultType":"entity","dataType":"","sqlType":"select","sqlString":"SELECT patientId FROM hz_customerfamily f,hz_customercard c WHERE f.id=c.customerFamilyId AND c.patientId<>\'\' and f.id=\'{I_customerFamilyId}\' AND f.customerUserId=\'{M_id}\' and c.hospitalId=\'{I_hospitalId}\'","infoString":"查询不到绑定卡的数据，请绑定"}', 
			'{"nodeType":"xml","parameter":"D_patientId,I_hospitalId","funcName":"waitingQueue","resultType":"entity","dataType":"","xmlString":"<?xml version=\"1.0\" encoding=\"UTF-8\"?><req><hospitalId>{I_hospitalId}</hospitalId><patientId>{D_patientId}</patientId></req>","replaceXmlData":"dateTime,clinicCode,patName,admLoc,admDoc,waitingNumber","returnXmlData":"one"}' 
			),  
		 
	); 
?>
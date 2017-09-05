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
	
	//病历查询带参数
	/*'emrMasterListMore'    => array(
			'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_status,I_zzStatus,I_applyType,I_consType","resultType":"rows","sqlType":"select","sqlString":"SELECT  COUNT(DISTINCT emrId) AS rows FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and status=\'{I_status}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and  status=\'{I_status}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and  status=\'{I_status}\' END )  AND CASE WHEN \'{I_consType}\'=\'1\'  THEN consType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_applyType}\'=\'1\'  THEN applyType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_zzStatus}\'=\'\'  THEN 1=1  ELSE zzStatus=\'{I_zzStatus}\'   END   {OhterSql}  ","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step2","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_status,I_zzStatus,I_applyType,I_consType,P_rowed,P_pageSize","resultType":"list","sqlType":"select","sqlString":"select DISTINCT emrId,itemCont,emrType,emrTypeId,hospitalId,status,createDate,updateDate from hz_view_emr_owner WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and status=\'{I_status}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and  status=\'{I_status}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and  status=\'{I_status}\' END )   AND CASE WHEN \'{I_consType}\'=\'1\'  THEN consType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_applyType}\'=\'1\'  THEN applyType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_zzStatus}\'=\'\'  THEN 1=1  ELSE zzStatus=\'{I_zzStatus}\'   END    {OhterSql}   ORDER BY updateDate desc {P_rowed}{P_pageSize} ","infoString":"未检查到数据"}',
			),
		*/
		
	'emrMasterListMore'    => array(
			'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_groupType,I_workStyle,I_applyType,I_consType","resultType":"rows","sqlType":"select","sqlString":"SELECT  COUNT(DISTINCT emrId) AS rows FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' AND consStatus< 2  END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\'  END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\'  END ) AND CASE WHEN \'{I_groupType}\' = \'0\' THEN STATUS = \'0\'  ELSE groupType=\'{I_groupType}\' AND  workStyle  in ({I_workStyle}) END AND CASE WHEN \'{I_consType}\'=\'1\'  THEN consType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_applyType}\'=\'1\'  THEN applyType = \'1\' ELSE 1=1  END   {OhterSql}  ","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step2","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_groupType,I_workStyle,I_applyType,I_consType,P_rowed,P_pageSize","resultType":"list","sqlType":"select","sqlString":"select DISTINCT emrId,itemCont,emrType,emrTypeId,hospitalId,status,createDate,updateDate from hz_view_emr_owner WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\'  AND consStatus< 2 END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' END )  AND CASE WHEN \'{I_groupType}\' = \'0\' THEN STATUS = \'0\'   ELSE groupType=\'{I_groupType}\' AND  workStyle in ({I_workStyle}) END AND CASE WHEN \'{I_consType}\'=\'1\'  THEN consType = \'1\' ELSE 1=1  END  AND CASE WHEN \'{I_applyType}\'=\'1\'  THEN applyType = \'1\' ELSE 1=1  END {OhterSql}   ORDER BY updateDate desc {P_rowed}{P_pageSize} ","infoString":"未检查到数据"}',
			),
	
	  //病历管理(整合)
	  'emrComposite'   => array(
			'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_emrId","resultType":"entity","sqlType":"select","sqlString":"SELECT DISTINCT emrId FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END )","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step2","parameter":"I_emrId,I_idNo","resultType":"entity","sqlType":"select","sqlString":"select emrId from hz_view_emr_owner where emrId=\'{I_emrId}\' and idNo=\'{I_idNo}\'","infoString":"查询不相关数据"}', 			
			'{"nodeType":"step3","parameter":"I_emrId","arrItem":"\'BCJL_BCDJ\',\'BCJL_BCDJ_SYS\',\'HZ_HZJL\',\'ZZ_ZZJL\',\'LIS_ITEM_SYS\',\'PACS_ITEM_SYS\',\'LIS_ITEM_IMG\',\'PACS_ITEM_IMG\',\'PACS_ITEM_DICOM\',\'OTHER_ITEM_FJ\',\'OTHER_FUJ\'","resultType":"list","sqlType":"select","sqlString":"SELECT d.* FROM hz_emr_detail d,hz_dict_emr_item i WHERE d.itemName=i.ename and d.emrId=\'{I_emrId}\' {OhterSql} ORDER BY i.sort,d.emrId ","infoString":"查询不相关数据"}',
			),  
		
		//病历管理(整合分享PS:注意上面的内容与上面的内容一致)
		'emrShare'   => array(
			'{"nodeType":"","parameter":"","arrItem":"\'BCJL_BCDJ\',\'BCJL_BCDJ_SYS\',\'HZ_HZJL\',\'ZZ_ZZJL\',\'LIS_ITEM_SYS\',\'PACS_ITEM_SYS\',\'LIS_ITEM_IMG\',\'PACS_ITEM_IMG\',\'PACS_ITEM_DICOM\',\'OTHER_ITEM_FJ\',\'OTHER_FUJ\'","resultType":"list","sqlType":"select","sqlString":"SELECT d.* FROM hz_emr_detail d,hz_dict_emr_item i WHERE d.itemName=i.ename {OhterSql} ORDER BY i.sort,d.emrId ","infoString":"查询不相关数据"}',
			),   
		
	
		//添加会诊流程
		//先判断有没有病历的权限，再添加OWER表里的数据
		'emrConsultation'   => array(
			//'{"nodeType":"step1","parameter":"M_userName,I_emrId","resultType":"entity","sqlType":"select","sqlString":"select emrId,status from hz_view_emr_owner_master where ownerId=\'{M_userName}\' and emrId=\'{I_emrId}\'","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_emrId","resultType":"entity","sqlType":"select","sqlString":"SELECT DISTINCT emrId,status FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END )","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step2","parameter":"I_emrId,json,I_invitedDoctor,I_applyDoctor,I_exStatus,F_timenow","resultType":"autoid","sqlType":"insert","sqlString":"","infoString":"添加数据失败"}',
			
			),   
		
		//会诊评价
		'emrConsultation'   => array(
		 	'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_emrId","resultType":"entity","sqlType":"select","sqlString":"SELECT DISTINCT emrId,status FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END )","infoString":"没有权限读该病历"}', 
			'{"nodeType":"step2","parameter":"I_emrId,I_commentInfo","resultType":"entity","sqlType":"","sqlString":"","infoString":"更新数据失败"}',
			
			),   

		//转诊申请
		'emrReferralApply'   => array(
			'{"nodeType":"step1","parameter":"M_userName,M_userType,M_hospitalId,M_deptId,I_emrId","resultType":"entity","sqlType":"select","sqlString":"SELECT DISTINCT emrId,status FROM hz_view_emr_owner t WHERE (CASE WHEN \'{M_userType}\'=\'1\'  THEN ownerId=\'{M_userName}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'2\' THEN deptId=\'{M_deptId}\' AND hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END OR CASE WHEN \'{M_userType}\'=\'3\'  THEN hospitalId=\'{M_hospitalId}\' and emrId=\'{I_emrId}\' END ) and status =\'0\' and  zzStatus in(\'0\',\'2\')","infoString":"该病历未达到转诊要求"}', 
			'{"nodeType":"step2","parameter":"I_emrId,I_idNo,I_hospitalId,I_deptId,I_doctorId","resultType":"","sqlType":"","sqlString":"","infoString":"添加数据失败"}',
			
			),  
		 
		); 
	
?>
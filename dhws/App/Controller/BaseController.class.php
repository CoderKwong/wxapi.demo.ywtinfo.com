<?php
if (!defined('IS_INITPHP')) exit('Access Denied!'); 

header("content-type:text/html;charset=utf-8");
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Methods:GET');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class BaseController extends Controller{
	protected function _init(){ 
		
		//引入Services文件 
		includeIfExist(C('APP_FULL_PATH').'/Service/BaseService.class.php');
		includeIfExist(C('APP_FULL_PATH').'/Service/ApiService.class.php');  
		includeIfExist(C('APP_FULL_PATH').'/Service/OtherService.class.php');  
		
		//引入dao和common文件    
		includeIfExist(C('APP_FULL_PATH').'/Dao/DatabaseDao.class.php');   
		includeIfExist(C('APP_FULL_PATH').'/Lib/common.class.php');  
		
		//加载配置文件
		include C('APP_FULL_PATH').'/FlowConfig/apiConfig.php';   
		include C('APP_FULL_PATH').'/FlowConfig/otherConfig.php';  
		$flowConfig = array_merge($apiConfig,$otherConfig); 
		C($flowConfig);    
		
		//引入 COMMON new 对象 
		$_ENV["commonClass"] = new commonClass();   
		//引入数据DAO层 
		$_ENV["dbDao"]	= new DatabaseDao();  
		
		//写入参考 日志 
		$note = "IP:".call_user_func(array($_ENV["commonClass"],"ip"))."  传入参数: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." POST:".iconv("UTF-8","GB2312//IGNORE",file_get_contents("php://input"));
		//$json = json_decode(iconv("GB2312","UTF-8//IGNORE",$json));   
		Log::getpost($note);  
		
	}   
	 
} 

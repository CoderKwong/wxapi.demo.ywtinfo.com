<?php
defined('IS_INITPHP') || die('Access Denied!');

/**
 * 扩展类库
 * 
 * @version 0.1.0
 * @author clh
 */
class commonClass{
	
	
	/**
	 *生成时间戳
	 * @return string
	 */
	function timestamp() {
		list($s1, $s2) = explode(' ', microtime());		
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)));
	}
	
	
	/**
	 * 生成时间
	 * @return string
	 */
	function timenow() { 
		return date("Y-m-d H:i:s", time());
	}
	
	
	/**
	* 生成时间
	* @return string
	*/
	function datenow() { 
		return date("Y-m-d", time());
	}
	
	
	
	/**
	* 生成时间
	* @return string
	*/
	function datemore() { 
		return date("Y-m-d", strtotime("+6 day"));
	}
	
	/**
	 * 生成时间戳大于 多少分钟，30分种有效期 
	 * @param $timestamp 时戳
	 * @return string
	 */
	function timestampeq($timestamp=0) {
		list($s1, $s2) = explode(' ', microtime());		
		return (float) floatval($timestamp)+ (floatval(60*3000));
	}
	
	/**
	 * 用户token 
	 * @param $uid 用户ID
	 * @param $timestamp 时戳
	 * @return string
	 */
	function token($uid=0,$timestamp=0) {  
		return md5($uid.$timestamp);
	}	
	
	
	/**
	 *生成时间戳+随机码 唯一订单ID号
	 * @return string
	 */
	function orderId() {
		list($s1, $s2) = explode(' ', microtime());		
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2))).$this->randomnum();
	}
	
	
	/**
	* 获取地址错误 
	*/
	function urlErr(){
		//jsonp回调参数，必需   
		$callback = isset($_GET['callback']) ? trim($_GET['callback']) : '';  
		$jsonData["dataToken"]["timestamp"]=""; 
		$jsonData["dataToken"]["token"]="";   
		$jsonData["dataToken"]["id"]="";  
		$jsonData["dataToken"]["status"]=0; 
		$jsonData["dataToken"]["info"]="验证失败"; 
		$jsonData["dataInfo"]["status"]="0"; 
		$jsonData["dataInfo"]["info"]="非法访问";
		$jsonData["dataInfo"]["data"]="";
		//返回格式，有回调或没有回调两种方式
		if($callback){
			echo $callback . '(' .json_encode($jsonData) .')';  //返回格式，回调来jsonp 必需 json 数据  
		}else{
			echo json_encode($jsonData);  //返回格式，必需 json 数据  
		}   
	}
	
	function array_multi2single($array)
	{
		static $result_array=array(); 
		foreach($array as $key=>$value)
		{
			if(is_array($value))
			{
				$this->array_multi2single($value);
			}
			else  
				$result_array[]=$value;
		}
		return $result_array;
	} 

	/**
	* 将多维数组转为一维数组
	* @author echo 
	* @param array $arr
	* @return array
	*/
	function ArrMd2Ud($arr) {
		#将数值第一元素作为容器，作地址赋值。
		$ar_room = &$arr[key($arr)];
		#第一容器不是数组进去转呀
		if (!is_array($ar_room)) {
			#转为成数组
			$ar_room = array($ar_room);
		}
		#指针下移
		next($arr);
		#遍历
		while (list($k, $v) = each($arr)) {
			#是数组就递归深挖，不是就转成数组
			$v = is_array($v) ? call_user_func(__FUNCTION__, $v) : array($v);
			#递归合并
			$ar_room = array_merge_recursive($ar_room, $v);
			#释放当前下标的数组元素
			unset($arr[$k]);
		}
		return $ar_room;
	}
	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
	 * @return mixed
	 */
	function ip($type = 0,$adv=false) {
		$type       =  $type ? 1 : 0;
		static $ip  =   NULL;
		if ($ip !== NULL) return $ip[$type];
		if($adv){
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$pos    =   array_search('unknown',$arr);
				if(false !== $pos) unset($arr[$pos]);
				$ip     =   trim($arr[0]);
			}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip     =   $_SERVER['HTTP_CLIENT_IP'];
			}elseif (isset($_SERVER['REMOTE_ADDR'])) {
				$ip     =   $_SERVER['REMOTE_ADDR'];
			}
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}

	//用php从身份证中提取生日,包括15位和18位身份证 
	function getIDCardInfo($IDCard){
		$result['error']=0;//0：未知错误，1：身份证格式错误，2：无错误
		$result['flag']='';//0标示成年，1标示未成年
		$result['tdate']='';//生日，格式如：2012-11-15
		if(!eregi("^[1-9]([0-9a-zA-Z]{17}|[0-9a-zA-Z]{14})$",$IDCard)){
			$result['error']=1;
			return $result;
		}else{
			if(strlen($IDCard)==18){
				$tyear=intval(substr($IDCard,6,4));
				$tmonth=intval(substr($IDCard,10,2));
				$tday=intval(substr($IDCard,12,2));
				if($tyear>date("Y")||$tyear<(date("Y")-100)){
					$flag=0;
				}elseif($tmonth<0||$tmonth>12){
					$flag=0;
				}elseif($tday<0||$tday>31){
					$flag=0;
				}else{
					$tdate=$tyear."-".$tmonth."-".$tday."";
					if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){
						$flag=0;
					}else{
						$flag=1;
					}
				}
			}elseif(strlen($IDCard)==15){
				$tyear=intval("19".substr($IDCard,6,2));
				$tmonth=intval(substr($IDCard,8,2));
				$tday=intval(substr($IDCard,10,2));
				if($tyear>date("Y")||$tyear<(date("Y")-100)){
					$flag=0;
				}elseif($tmonth<0||$tmonth>12){
					$flag=0;
				}elseif($tday<0||$tday>31){
					$flag=0;
				}else{
					$tdate=$tyear."-".$tmonth."-".$tday."";
					if((time()-mktime(0,0,0,$tmonth,$tday,$tyear))>18*365*24*60*60){
						$flag=0;
					}else{
						$flag=1;
					}
				}
			}
		}
		$result['error']=2;//0：未知错误，1：身份证格式错误，2：无错误
		$result['isAdult']=$flag;//0标示成年，1标示未成年
		$result['birthday']=$tdate;//生日日期
		return $result;
	}
	
	
	
	/**
	 * 发送HTTP状态
	 * @param integer $code 状态码
	 * @return void
	 */
	function send_http_status($code) {
		static $_status = array(
			// Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			// Success 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			// Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily ',  // 1.1
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			// 306 is deprecated but reserved
			307 => 'Temporary Redirect',
			// Client Error 4xx
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			// Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			509 => 'Bandwidth Limit Exceeded'
			);
		if(isset($_status[$code])) {
			header('HTTP/1.1 '.$code.' '.$_status[$code]);
			// 确保FastCGI模式下正常
			header('Status:'.$code.' '.$_status[$code]);
		}
	}

	function think_filter(&$value){
		// TODO 其他安全过滤

		// 过滤查询特殊字符
		if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
			$value .= ' ';
		}
	}

	// 不区分大小写的in_array实现
	function in_array_case($value,$array){
		return in_array(strtolower($value),array_map('strtolower',$array));
	}

	/**
	 * 对用户的密码进行加密
	 * @param $password
	 * @param $encrypt //传入加密串，在修改密码时做认证
	 * @return array/password
	 */
	function password($password, $encrypt='') {
		$pwd = array();
		$pwd['encrypt'] =  $encrypt ? $encrypt : getRandChar();
		$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
		return $encrypt ? $pwd['password'] : $pwd;
	}

	/**
	 * 生成随机字符串
	 * @param string $lenth 长度
	 * @return string 字符串
	 */
	function randomstr($lenth = 6) {
		return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
	}
	
	
	/**
	 * 生成随机数字串
	 * @param string $lenth 长度
	 * @return string 字符串
	 */
	function randomnum() {
	
		$str = null;
		$strPol = "0123456789";
		$max = strlen($strPol)-1;

		for($i=0;$i<6;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	} 
	 
	function getRandChar($length=6){
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;

		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	} 
	
	function post_check($post)     
	{     
		if (!get_magic_quotes_gpc()) // 判断magic_quotes_gpc是否为打开     
		{     
			$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤     
		}     
		$post = str_replace("_", "\_", $post); // 把 '_'过滤掉     
		$post = str_replace("%", "\%", $post); // 把' % '过滤掉     
		$post = nl2br($post); // 回车转换     
		$post= htmlspecialchars($post); // html标记转换        
		return $post;     
	} 
	
	/**  
	* 发送get请求  
	* @param string $url 请求地址  
	* @param array $post_data post键值对数据  
	* @return string  
	*/  
	function send_get($url) {    
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec($ch);  
		return $result;   
	}
	
	
	/**  
	* 发送post请求  
	* @param string $url 请求地址  
	* @param array $post_data post键值对数据  
	* @return string  
	*/  
	function send_post($url, $post_data) {   
		
		$postdata = http_build_query($post_data);   
		$options = array(   
			'http' => array(   
					'method' => 'POST',   
					'header' => 'Content-type:application/x-www-form-urlencoded',   
					'content' => $postdata,   
					'timeout' => 15 * 60 // 超时时间（单位:s）   
					)   
				);   
		$context = stream_context_create($options);   
		$result = file_get_contents($url, false, $context);    
		return $result;   
	} 
	 
	/**
	 * php异步请求
	 * $args array[
	 *   "host":主机
	 *   "url":地址
	 *   "method":方法
	 *   "data":数据
	 * ]
	 */
	function asyn_request($args) {
		$host = $args["host"] ?  $args["host"] : "localhost";//主机
		$method = $args["method"] == "POST" ? "POST" : "GET";//方法    
		$url = $args["url"] ? $args["url"] : "http://".$host ;//地址
		$data = is_array($args["data"]) ? $args["data"] : array();//请求参数    
		$fp = @fsockopen($host,80,$errno,$errstr,30); 
		//错误
		if(!$fp){echo"$errstr ($errno)<br/>\n";exit;}
    
		$qstr = http_build_query($data);//请求参数
    
		$params.= $method == "GET" ? "GET {$url}?{$qstr} HTTP/1.1\r\n" :  "POST {$url} HTTP/1.1\r\n";
		$params.= "Host: ".$host."\r\n";
		$params.= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5\r\n";
		$params.= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
		$params.= "Accept-Language: zh-cn,zh;q=0.5\r\n";
		$params.= "Accept-Encoding: gzip,deflate\r\n";
		$params.= "Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7\r\n";
		$params.= "Keep-Alive: 300\r\n";
		$params.= "Connection: keep-alive\r\n";
		$params.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$params.= "Content-Length: ".strlen($qstr)."\r\n\r\n";
		$params.= $method == "GET" ? null :$qstr;
    
		//file_put_contents("C:\\http.txt",$params);
    
		fwrite($fp, $params);
		fclose($fp);
		
		
	/*	$args["host"] = "localhost";//主机
		$args["url"] = "http://localhost/test/socket/doing.php";//异步执行的脚本
		$args["method"] = "POST";//请求方式
		$args["data"] = array("a"=>"中","b"=>"国");//参数 
		asyn_request($args);*/
		
	}
	
	
	//发送XML返回数据
	function sendDataByCurl($url,$data){
		
		//Log::posthis("QuerySchedule:req2\r\n"."*a".call_user_func(array($_ENV["commonClass"],"timestamp")));  
		$header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_TIMEOUT,60);  //定义超时3秒钟  
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
		$response = curl_exec($ch);
		
		//Log::posthis("QuerySchedule:req2\r\n"."*b".call_user_func(array($_ENV["commonClass"],"timestamp")));
		if(curl_errno($ch)){ 
			Log::cur_err(curl_error($ch)); 
		}
		curl_close($ch); 
		
		
		//Log::posthis("QuerySchedule:req2\r\n"."*c".call_user_func(array($_ENV["commonClass"],"timestamp")));
		Log::posthis($url.$data); 
		 
		try{
			$xml = simplexml_load_string($response); 
			header("Content-Type: text/html; charset=UTF-8");
			if($xml) {  
				Log::gethis($response); 
				
			} else {
				Log::xmlerr($response); 
			}  
			
		} catch(exception $e){
			Log::xmltc($e);  
		} 
		return $xml;
	}
	
	
	
	//发送XML返回数据
	function sendDataByCurlStr($url,$data){
		
		$header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_TIMEOUT,60);  //定义超时3秒钟  
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
		$response = curl_exec($ch);
		if(curl_errno($ch)){ 
			Log::cur_err(curl_error($ch)); 
		}
		curl_close($ch); 
		
		Log::posthis($url.$data);  
		try{ 
			$response= simplexml_load_string($response);
			header("Content-Type: text/html; charset=UTF-8");
			if($response) { 
				Log::gethis($response); 
			} else {
				Log::xmlerr($response); 
			}  
			
		} catch(exception $e){
			Log::xmltc($e);  
		}
		
		return $response;
	}
} 

?>

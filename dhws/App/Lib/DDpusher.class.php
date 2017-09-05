<?php
 
/**
 * 用Socket向DDpush服务器发送消息
 * 相关文档，请参考http://www.ddpush.net
 * @author Wang Wenbing<binny_w@qq.com>
 */
class DDpusher { 
    /* Socket resource */
    private $socket = null;
    
    /**
     * 构造函数
     * @param string $strHost
     * @param int $intPort
     * @throws Exception
     */
    public function __construct($strHost, $intPort = 9999) {
        $strHost = strval($strHost);
        $intPort = intval($intPort);
        if (empty($strHost) || !$intPort) {
            throw new Exception('Wrong strHost or Wrong intPort');
        } elseif (($this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            throw new Exception('Error at socket_create(): ' . socket_strerror(socket_last_error()));
        } elseif (socket_connect($this->socket, $strHost, $intPort) === false) {
            throw new Exception('Error at socket_connect(): ' . socket_strerror(socket_last_error()));
        }
    }
    
    /**
     * 生成32位的UUID，可以重写此函数
     * @param string $strUser
     * @return string(32)
     */
    private function getUUID($strUser) {
        $strUser = trim($strUser);
        //return strlen($strUser) ? md5($strUser) : false;
		return $strUser;
    }

    /**
     * 检查Version和Appid参数
     * @param int $intVersion
     * @param int $intAppid
     * @return boolen
     */
    private function checkVersionAndAppid($intVersion, $intAppid) {
        return ($intVersion > 0 && $intVersion < 256 && $intAppid > 0 && $intAppid < 256);
    }
    
    /**
     * 发送通知
     * @param string $strUser
     * @return boolean $blnRe
     */
	public function push0x10($strUUID, $intVersion = 1, $intAppid = 1) {
        $blnRe = false;
        $intVersion = intval($intVersion);
        $intAppid = intval($intAppid);
		if ($this->checkVersionAndAppid($intVersion, $intAppid) && $strUUID !== false && $this->socket) {
            $strBin = pack('CCCH32n', $intVersion, $intAppid, 16, $strUUID, 0);
            socket_write($this->socket, $strBin, strlen($strBin)) && $blnRe = (bindec(socket_read($this->socket, 1)) == 0);
        } else {
            throw new Exception('Error at push0x10()');
        }
        return $blnRe;
    }
    
    /**
     * 发送分类信息
     * @param string $strUser
     * @param string $strHex 16位长的16进制字符
     * @param int $intVersion
     * @param int $intAppid
     * @return boolen $blnRe
     */
	public function push0x11($strUUID, $strHex, $intVersion = 1, $intAppid = 1) {
        $blnRe = false;
        $intVersion = intval($intVersion);
        $intAppid = intval($intAppid);
        $strHex = trim($strHex);
		if ($this->checkVersionAndAppid($intVersion, $intAppid) && $strUUID !== false && $this->socket && strlen($strHex) == 16) {
            $strBin = pack('CCCH32nH16', $intVersion, $intAppid, 17, $strUUID, 8, $strHex);
            socket_write($this->socket, $strBin, strlen($strBin)) && $blnRe = (bindec(socket_read($this->socket, 1)) == 0);
        } else {
            throw new Exception('Error at push0x11()');
        }
        return $blnRe;
    }
    
    /**
     * 发送500字节以内的字符消息
     * @param string $strUser
     * @param string $strMsg 必须是utf8编码的字符
     * @param int $intVersion
     * @param int $intAppid
     * @return boolen $blnRe
     * @throws Exception
     */
	public function push0x20($strUUID, $strMsg, $intVersion = 1, $intAppid = 1) {
        $blnRe = false;
        $intVersion = intval($intVersion);
        $intAppid = intval($intAppid);
        // $strMsg = mb_convert_encoding($strMsg, 'utf8', 'gbk');
        $strMsg = trim($strMsg);
        $intLen = strlen($strMsg);
        $blnTemp = ($intLen > 0 && $intLen <= 500);
		if ($this->checkVersionAndAppid($intVersion, $intAppid) && $strUUID !== false && $this->socket && $blnTemp) {
            $strBin = pack('CCCH32nA' . $intLen, $intVersion, $intAppid, 32, $strUUID, $intLen, $strMsg);
            socket_write($this->socket, $strBin, strlen($strBin)) && $blnRe = (bindec(socket_read($this->socket, 1)) == 0);
        } else {
            throw new Exception('Error at push0x20()');
        }
        return $blnRe;
    }
    
    /**
     * 断开连接
     */
    public function __destruct() {
        if ($this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }
    
}

//try { 
//	$obj = new DDpusher('127.0.0.1');
//  //$obj->push0x10('99000316797317') && print('通知已发送<br />');
//	// $obj->push0x11('99000316797317', '0000000000000001') && print('分类已发送<br />'); 
//	$obj->push0x20('162434c1879fcc3d0c098d9a6af4da6a', '提示内容@http://www.baidu.com@3') && print('字符串消息已发送<br />'); 
//} catch (Exception $ex) {
//	echo $ex->getMessage();
//} 
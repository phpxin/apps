<?php
include_once 'tools.php' ;
include_once '../dboper.class.php' ;

class recvMsg{

	const TOKEN = "a1Hy2s3S456*(Db)ac" ;
	private $requestData ;
	private $requestParams ;
	private $msgType ;


	public function __construct(){
		$this->check() ;
		$this->parseRequest() ;
		$this->distribute() ;
	}

	private function recvEvent(){

		try{
			$db = dboper::inst('bigscreen_server') ;
		

			//解析消息
			if ($this->requestParams['Event']=='subscribe') {
				$openid = $this->requestParams['FromUserName'] ;

				$info = $db->select("select id from user where `openid`=:openid limit 1", [':openid'=>$openid]);
				if (!$info) {
					$db->add("insert into user(`openid`,`created_at`) values(:openid, :created_at)", [':openid'=>$openid,':created_at'=>time()]);
				}

				

			}
		}catch(Exception $e){
			writeLog($e->getMessage()); 
		}
		

	}

	private function distribute(){
		switch ($this->msgType) {
			case 'event':
				$this->recvEvent();
				break;
			
			default:
				# code...
				break;
		}

		return true;
	}

	private function check(){
		$check['token'] = self::TOKEN ;
		$check['timestamp'] = $_GET['timestamp'] ;
		$check['nonce'] = $_GET['nonce'] ;

		writeLog($check);

		$signature = $_GET['signature'] ;
		$echostr = $_GET['echostr'] ;

		sort($check) ;

		$my_s = '' ;

		foreach ($check as $key => $value) {
			# code...
			$my_s .= $value ;
		}

		$my_s = sha1($my_s);

		if($my_s == $signature){
			echo $echostr ;
		}else {
			echo 'failed' ; // 不合法
			exit();
		}

		return true ;
	}

	private function parseRequest() {
		$requestData = file_get_contents("php://input");
		writeLog($requestData);


		$xml = new DOMDocument() ;
		$xml->loadXML($requestData) ;

		$ret = [] ;

		$_ele = $xml->getElementsByTagName('MsgType')->item(0);
		$ret['MsgType'] = $_ele->nodeValue ;
		$this->msgType = $_ele->nodeValue ;

		if($ret['MsgType']=='event'){ // 微信事件
			$_ele = $xml->getElementsByTagName('Event')->item(0);
			$ret['Event'] = $_ele->nodeValue ;

			if ($ret['Event'] == 'subscribe') { // 关注
				$_ele = $xml->getElementsByTagName('ToUserName')->item(0);
				$ret['ToUserName'] = $_ele->nodeValue ;
				$_ele = $xml->getElementsByTagName('FromUserName')->item(0);
				$ret['FromUserName'] = $_ele->nodeValue ;
				$_ele = $xml->getElementsByTagName('CreateTime')->item(0);
				$ret['CreateTime'] = $_ele->nodeValue ;
				$_ele = $xml->getElementsByTagName('EventKey')->item(0);
				$ret['EventKey'] = $_ele->nodeValue ;
			}
		}

		$this->requestParams = $ret ;

		return true;
	}
}


new recvMsg();

?>
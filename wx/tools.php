<?php
define('APP_ID', 'wx43bb66a03f3f756a');
define('APP_SEC', '7c3d2287a74ea05ad9882ed6d641aa96') ;

function postRequest($url, $fields){
        $header = array(
            "Content-Type:application/json",
            'User-Agent: Mozilla/4.0 (compatible; MSIE .0; Windows NT 6.1; Trident/4.0; SLCC2;)');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        curl_close($ch);
        if ($result == NULL) {
            return 0;
        }
        return $result;
}

function getWxToken(){

	$tokenPath = '../logs/wxtoken.json' ;

	if (file_exists($tokenPath )) {
		# code...
		$tokenInfo = file_get_contents($tokenPath) ;
		$tokenInfo = json_decode($tokenInfo, true) ;
		if ($tokenInfo['lost_time']>time()) {
			writeLog('cached token info is '.var_export($tokenInfo, true));
			# code...
			return $tokenInfo['access_token'] ;
		}
	}


	$tokenInfo = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APP_ID."&secret=".APP_SEC) ;
	writeLog('token info is '.$tokenInfo);
	if (empty($tokenInfo)) {
		# code...
		return false;
	}

	$tokenInfo = json_decode($tokenInfo, true) ;
	if (isset($tokenInfo['errcode']) && $tokenInfo['errcode']) {
		# code...
		return false;
	}

	
	$tokenInfo['lost_time'] = $tokenInfo['expires_in'] + time();
	file_put_contents($tokenPath , json_encode($tokenInfo)) ;

	return $tokenInfo['access_token'] ;
}


function writeLog($msg){
	if (!is_string($msg)) {
		# code...
		$msg = var_export($msg, true) ;
	}

	file_put_contents('../logs/'.date('Y-m').'.log', date('Y-m-d H:i').' '.$msg.PHP_EOL, FILE_APPEND) ;

}
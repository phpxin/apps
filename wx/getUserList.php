<?php
include 'tools.php' ;


$accessToken = getWxToken();
if (!$accessToken) {
	# code...

	echo 'failed' ;
	exit();
}

$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken.'&next_openid=' ;
$openidList = file_get_contents($url);
writeLog('open id list '.$openidList); 

if (empty($openidList)) {
	# code...
	echo 'failed:user list get';
	exit();
}

$openidList = json_decode($openidList, true) ;
if (isset($openidList['errcode']) && $openidList['errcode']) {
	# code...
	echo 'failed:user list get , errcode '. $openidList['errcode'] ;
	exit();
}



//{"total":1,"count":1,"data":{"openid":["oleSgxNUYBCAm2i8n6Jj-eLNt4mY"]},"next_openid":"oleSgxNUYBCAm2i8n6Jj-eLNt4mY"}


$reqUserList['user_list'] = [] ;
foreach ($openidList['data']['openid'] as $value) {
	# code...
	$reqUserList['user_list'][] = [
		'openid' => $value ,
		'lang' => 'zh_CN'
	];
}
$reqUserList = json_encode($reqUserList) ;
writeLog('request user list is '.$reqUserList);
$url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$accessToken ;
$response = postRequest($url, $reqUserList);
if (!$response) {
	# code...
	echo 'failed: user info list get result is null' ;
	exit();
}


$response = json_decode($response, true) ;
if (isset($response['errcode']) && $response['errcode']) {
	# code...
	echo 'failed: user info list get , errcode '. $response['errcode'] ;
	exit();
}



var_dump($response) ;

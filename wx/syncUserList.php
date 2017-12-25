<?php
include 'tools.php' ;
include '../dboper.class.php' ;


writeLog('get '.var_export($_REQUEST, true));


$synctime = $_REQUEST['synctime'] ;

$accessToken = getWxToken();
if (!$accessToken) {
	json_error('token err') ;
}

$db = dboper::inst('bigscreen_server') ;
$openidList = $db->select("select openid from user where created_at>:synctime and is_sync=0", [":synctime"=>$synctime]);
if (!$openidList) {
	json_success(['list'=>[], 'synctime'=>time()]) ;//  没有需要同步的用户数据，返回空数组
}

$reqUserList['user_list'] = [] ;
foreach ($openidList as $value) {
	
	$reqUserList['user_list'][] = [
		'openid' => $value['openid'] ,
		'lang' => 'zh_CN'
	];
}

$reqUserList = json_encode($reqUserList) ;
writeLog('request user list is '.$reqUserList);
$url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$accessToken ;
$response = postRequest($url, $reqUserList);
writeLog("syncUserList : user info get response ".var_export($response, true)) ;
if (!$response) {
	json_error('sync user failed') ;
}


$response = json_decode($response, true) ;
if (isset($response['errcode']) && $response['errcode']) {
	json_error('sync user failed') ;
}

$list = [] ;
foreach ($response['user_info_list'] as $key => $value) {
	$list[] = [
		'openid' => $value['openid'] ,
		'nickname' => $value['nickname'] ,
		'headimgurl' => $value['headimgurl'] ,
	] ;
}

$ret['list'] = $list ;
$ret['synctime'] => time();
json_success($ret) ;
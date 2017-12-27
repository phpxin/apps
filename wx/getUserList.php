<?php
include 'tools.php' ;
include '../dboper.class.php' ;

writeLog('post '.var_export($_POST, true));
$existOpenids = [] ;
if (isset($_POST['openids']) && !empty($_POST['openids'])) {
	$existOpenids = explode(',', trim($_POST['openids'], ' ,')) ;
}
writeLog('exist openids '.var_export($existOpenids, true));

$accessToken = getWxToken();
if (!$accessToken) {
	# code...

	//echo 'failed' ;
	writeLog('getUserList : failed to get token');
	json_error('token err') ;
	exit();
}

$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken.'&next_openid=' ;
$openidList = file_get_contents($url);
writeLog('open id list '.$openidList); 

if (empty($openidList)) {
	# code...
	//echo 'failed:user list get';
	writeLog('getUserList : failed to get user list ');
	json_error('failed:user list get') ;
	exit();
}

$openidList = json_decode($openidList, true) ;
if (isset($openidList['errcode']) && $openidList['errcode']) {
	# code...
	//echo 'failed:user list get , errcode '. $openidList['errcode'] ;
	writeLog('getUserList : failed to get user list ');
	json_error('failed:user list get') ;
	exit();
}

$reqUserList['user_list'] = [] ;
foreach ($openidList['data']['openid'] as $value) {
	
	if (in_array($value, $existOpenids)) {
		//openid 客户端已存在，忽略之
		continue ;
	}

	$reqUserList['user_list'][] = [
		'openid' => $value ,
		'lang' => 'zh_CN'
	];
}

if (empty($reqUserList['user_list'])){
	json_success(['list'=>[], 'synctime'=>time()]) ;//  没有需要同步的用户数据，返回空数组
}

$reqUserList = json_encode($reqUserList) ;
writeLog('request user list is '.$reqUserList);
$url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$accessToken ;
$response = postRequest($url, $reqUserList);
if (!$response) {
	# code...
	//echo 'failed: user info list get result is null' ;
	writeLog('getUserList : user info list get result is null');
	json_error('failed: user info list get result is null') ;
	exit();
}


$response = json_decode($response, true) ;
if (isset($response['errcode']) && $response['errcode']) {
	# code...
	//echo 'failed: user info list get , errcode '. $response['errcode'] ;
	writeLog('getUserList : user info list get , errcode '. $response['errcode']);
	json_error('failed: user info list get , errcode '. $response['errcode']) ;
	exit();
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
$ret['synctime'] = time();
json_success($ret) ;
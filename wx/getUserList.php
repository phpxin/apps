<?php
incldue 'tools.php' ;


$accessToken = getWxToken();
if (!$accessToken) {
	# code...

	echo 'failed' ;
	exit();
}



$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken.'&next_openid=' ;
$openidList = file_get_contents($url);

var_dump($openidList) ;


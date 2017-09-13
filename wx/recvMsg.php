<?php

$check['token'] = "a1Hy2s3S456*(Db)ac" ;
$check['timestamp'] = $_GET['timestamp'] ;
$check['nonce'] = $_GET['nonce'] ;

file_put_contents('./logs/a.log', var_export($_GET, true).PHP_EOL) ;

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
	echo 'failed' ;
}

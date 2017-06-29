<?php
/**
 * 我想请个假
 */
//header ( "content-type:image/jpeg; charset=utf-8" );
ob_start();
$image = imagecreatefrompng ( './imgs/pic_3.png' );
imagesavealpha ( $image , 1 );

$nickname = trim(isset($_GET['nickname']) ? $_GET['nickname'] : '');
if(empty($nickname)){
	$nickname = '无名氏' ;
}

$qj_color = imagecolorallocatealpha ( $image, 0, 0, 0, 0 ); // 前景

// 设置
imageantialias ( $image, true ); // 抗锯齿

// 正文
$content = "{$nickname}最近负能量爆棚，分分钟易燃易爆，需要在7月份去青海湖打一局紧张又刺激的王者荣耀";
$charset = 'UTF-8' ;
$elen = 25 ;
$totalLine=ceil(mb_strlen($content, $charset)/$elen);
$fontPath = './fonts/avatar.ttf';
for($i=0; $i<$totalLine; $i++ ){
	$val = mb_substr($content, $i*$elen, $elen, $charset);
	$row = $i+1;
	imagettftext ( $image, 14, 0, 96, 226+ 30 * $row, $qj_color, $fontPath, $val );
}


imagettftext ( $image, 14, 0, 160, 142, $qj_color, $fontPath, $nickname );

$date = date("Y-m-d");
imagettftext ( $image, 14, 0, 404, 142, $qj_color, $fontPath, $date );

//表情
$bqindex = rand(1,3);
$bqpath = './imgs/bq'.$bqindex.'.jpg';
$bqImg = imagecreatefromjpeg($bqpath);
$bqInfo = getimagesize($bqpath);
//索引 0 包含图像宽度的像素值，索引 1 包含图像高度的像素值。
imagecopyresized ( $image , $bqImg , 404 , 330 , 0 , 0 , 160 , 160 , $bqInfo[0] , $bqInfo[1] ) ;

imagepng ( $image );

imagedestroy ( $image );

$imgContent = ob_get_contents();
ob_end_clean();

$imgContent = base64_encode($imgContent);

$device = 'pc' ;


if(isset($_SERVER['HTTP_USER_AGENT'])){
	
	if(preg_match('/(iphone|android){1}/iU',$_SERVER['HTTP_USER_AGENT'])){
		$device = 'h5' ;
	}
}



?>
<?php if($device=='pc'){ ?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<title>逗比请假条</title>
<meta name="Keywords" content="逗比请假条" />
<meta name="description" content="逗比请假条" />

</head>
<body>
<p>
<form method="get">
<input type="text" name="nickname" placeholder="请输入姓名" /><button type="submit">生成请假条</button>
</form>
</p>
<img src="data:image/png;base64,<?php echo $imgContent?>" />
<p style="color:red; font-weight:bold;" >PS:长按图片保存</p>
</body>
</html>

<?php }else{ ?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<title>逗比请假条</title>
<meta name="Keywords" content="逗比请假条" />
<meta name="description" content="逗比请假条" />
<meta content="width=device-width, minimum-scale=1,initial-scale=1, maximum-scale=1, user-scalable=1;" id="viewport" name="viewport" />
</head>
<body>
<p>
<form method="get">
<input type="text" name="nickname" placeholder="请输入姓名" /><button type="submit">生成请假条</button>
</form>
</p>
<img src="data:image/png;base64,<?php echo $imgContent?>" style="width:100%" />
<p style="color:red;font-weight:bold;" >PS:长按图片保存</p>
</body>
</html>


<?php } ?>
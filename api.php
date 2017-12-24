<?php
header("Content-Type: application/json") ;
include_once 'dboper.class.php' ;

$dba = dboper::inst("testt") ;
$list = $dba->select("select * from demo");

echo json_encode($list) ;
exit();
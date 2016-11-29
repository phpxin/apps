<?php
header("Content-Type: application/json") ;
include("config.php");
$db = new PDO("mysql:host=127.0.0.1; dbname=test", DB_USER, DB_PWD) ;

$db->query("set names utf8");

$stat = $db->query("select * from demo") ;

$rows = $stat->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows) ;
exit();
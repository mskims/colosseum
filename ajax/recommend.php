<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";

$idx = isset($_GET["idx"]) ? $_GET["idx"] : NULL;


if(!$logined){
	error("01");
}
if($idx == NULL){
	error("02");
}
$sql = "select count(*) as cnt from board where idx={$idx}";
$data = sqlf($sql);
if($data["cnt"] == 0) {
	error("03");
}

$sql = "select count(*) as cnt from recommend where idx_b={$idx} and idx_u={$_SESSION['member']['idx']}";
$data = sqlf($sql);
if($data["cnt"] >= 1){
	error("98");
}

$sql = "insert into recommend set idx_b={$idx}, idx_u={$_SESSION['member']['idx']}, type=1";
$db->query($sql) or die("E02");


$sql = "select count(*) as cnt from recommend where idx_b={$idx}";
$data = sqlf($sql);
echo $data["cnt"];


function error($errNum){
	echo "E".$errNum;
	exit();
}
?>
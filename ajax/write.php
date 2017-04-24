<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";

if(!$logined){
	error("01");
}
if(!isset($_POST["code"]) || !isset($_POST["subject"]) || !isset($_POST["content"]) || !isset($_POST["opinion"])){
	error("02");
}
if(strlen(trim($_POST["subject"])) < 1 || strlen(trim($_POST["content"])) < 1 ){
	error("03");
}
$chk1Data = sqlf("select id from board where code={$_POST['code']} limit 1");
if($chk1Data["id"] == $_SESSION["member"]["idx"]){
	error("04");
}


sql("insert into board set code={$_POST['code']}, subject='{$_POST['subject']}', content='{$_POST['content']}', opinion={$_POST['opinion']}, id={$_SESSION['member']['idx']}");
$insertIdx = $db->lastInsertId();
echo $insertIdx;


function error($errNum){
	echo "E".$errNum;
	exit();
}
?>
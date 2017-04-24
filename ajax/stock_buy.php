<?php
/*
에러코드


00 쿼리 오류
01 잘못된 값
02 잔액 부족
03 잔여 수량 부족

99 성공
*/
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";

$code = $_POST["code"];
$count = $_POST["count"];


if($count <= 0){
	echo "01";
	exit();
}
$sql3 = "select stock.*, sum(member_stock.count) as all_count from stock left join member_stock on stock.code = member_stock.code where stock.code='{$code}'";
$data_s = $db->query($sql3)->fetch();

if($data_s["all_count"]==NULL){
	$data_s["all_count"] = 0;
}

$sql = "select * from member where idx={$_SESSION['member']['idx']}";
$data_m = $db->query($sql)->fetch();

$cost = $data_s["price"] * $count;

if($data_s["all_count"]+$count > $data_s["stock_count"]){
	echo "03/".($data_s["stock_count"]-$data_s["all_count"]);
	exit();
}else if($data_m["balance"]-$cost < 0){
	echo "02";
	exit;
}else{
	$sql = "update member set balance = balance - {$cost} where idx={$_SESSION['member']['idx']}";
	$db->query($sql) or die("00");

	$sql = "insert into member_stock set s_idx=0, code='{$code}', type='SIMUL', id='{$_SESSION['member']['id']}', buy_price={$data_s['price']}, count={$count}";
	$db->query($sql) or die("00");

	echo "99";
	exit();
}

?>
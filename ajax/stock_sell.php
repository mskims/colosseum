<?php
/*
에러코드

01 잘못된 값
03 보유량 부족

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


$data_s = sqlf("select stock.code, stock.price, sum(member_stock.count) as stock_cnt from member_stock join stock on member_stock.code=stock.code where member_stock.code='{$code}' and member_stock.id='{$_SESSION['member']['id']}'");

if($data_s["stock_cnt"] < $count || $data_s["stock_cnt"]==NULL){
	if($data_s["stock_cnt"] == NULL){
		$data_s["stock_cnt"] = 0;
	}
	echo "03/{$data_s['stock_cnt']}";
	exit();
}

$to_sell = $count;
$res = sql("select * from member_stock where code='{$code}' and id='{$_SESSION['member']['id']}' order by count desc");



while($data=$res->fetch()){
	if($to_sell == 0){
		break;
	}
	$cnt =$data["count"];
	if($cnt < $to_sell || $cnt == $to_sell){
		$to_sell = $to_sell - $cnt;
		sql("delete from member_stock where idx={$data['idx']}");
	}else if($cnt > $to_sell){
		sql("update member_stock set count=count-{$to_sell} where idx={$data['idx']}");
	}
}
$sql ="update member set balance=balance+({$data_s['price']}*{$count}) where idx={$_SESSION['member']['idx']}";
sql($sql);
echo "99";

?>
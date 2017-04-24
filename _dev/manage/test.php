<?php
include_once "lib.php";

$sql = "select * from stock";
$result=$db->query($sql);

while($data=$result->fetch()){
	$selector = date("Y-m-d", strtotime("-1 days"))." 23:00:00";
	$sql = "select * from canvas where code='{$data['code']}' and regdate='{$selector}' ";
	$data_y = $db->query($sql)->fetch();

	$sql = "select * from canvas where regdate='{$selector}' and code='{$data['code']}'";
	$data_c = $db->query($sql)->fetch();
	$sql = "update stock set y_price={$data_c['price']}, c_percent=".(($data['price']-$data_c['price'])/$data_c['price']*100)." where code='{$data['code']}'";
	$db->query($sql);
}
?>
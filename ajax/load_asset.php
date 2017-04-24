<?php
/* 
주식자산/현금자산/합계
*/
session_start();
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";

$id = $_POST["id"];


$balance = 0;

// STOCK
$sql = " select
	sum(
		case when id = '{$id}' then count * stock.price end
	) as stock_asset,
   (select balance from member where id='{$id}') as balance
from 
	member_stock 
	join stock on member_stock.code = stock.code";
$sum_stock = sqlf($sql);




echo num_to_han_s($sum_stock["stock_asset"], getMoneyUnit($sum_stock["stock_asset"]));
// STOCK


// BALANCE
$balance = $sum_stock["balance"];
$_SESSION["member"]["balance"] = $balance;
echo "/".num_to_han_s($balance, getMoneyUnit($balance));
// BALANCE


// ALL
$sum = str_replace(",", "", number_format($sum_stock["stock_asset"] + $balance));
echo "/".num_to_han_s($sum, getMoneyUnit($sum));
// ALL


// RATIO

$id = $_SESSION['member']['id'];

//$sql = "select member_stock.*, sum(member_stock.count) as sum_count, stock.* from member_stock join stock on member_stock.code = stock.code where id='{$id}' group by member_stock.code";
$sql = "
select 
	member_stock.*,
	sum(member_stock.count) as sum_count,
	sum(member_stock.buy_price*member_stock.count)/sum(member_stock.count) as avg_price,
	stock.title,
	stock.price,
	stock.c_percent
from member_stock	
join stock
on member_stock.code = stock.code
where id='{$id}'
group by member_stock.code
order by (sum(member_stock.buy_price*member_stock.count)/sum(member_stock.count)*sum(member_stock.count)) desc
";
$result_sd = $db->query($sql);
$ori_sum = 0;
$now_sum = 0;
while($data_sd=$result_sd->fetch()){
	$ori_sum += $data_sd["avg_price"] * $data_sd["sum_count"];
	$now_sum += $data_sd["price"] * $data_sd["sum_count"];
	$ori = $data_sd["avg_price"]*$data_sd["sum_count"];
	$now = $data_sd["price"]*$data_sd["sum_count"];
	$gain = $now - $ori;
	$up = $gain > 0 ? true : false;
	$gain_percent = $gain / $ori * 100;	
}
$ratio = $ori_sum==0&&$now_sum==0 ? 0 : ( $now_sum - $ori_sum ) / $ori_sum * 100;
echo "/".sprintf("%.5f", $ratio)."%";

?>
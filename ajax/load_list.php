<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";


$type = $_POST["type"];
$limit = $_POST["limit"] != 0 ? $_POST["limit"] : 1000000;

$y = date("Y");
$m = date("m");
$d = date("d");

$h = date("H");

switch($type){
	case "heavy":
		$sql = "select * from stock order by price*stock_count desc limit {$limit}";
		$result = $db->query($sql);
		break;
	case "trade_price":
		$sql = "select canvas.code, sum(canvas.trade_count*stock.price) as trade_price, stock.title from canvas join stock on canvas.code = stock.code where year={$y} and month={$m} and day={$d} and canvas.code<>'000000' group by code order by trade_price desc limit {$limit}";
		$result = $db->query($sql);
		break;
	case "trade_count":
		$sql = "select canvas.code, sum(canvas.trade_count) as trade_count, stock.title from canvas join stock on canvas.code = stock.code where year={$y} and month={$m} and day={$d} and canvas.code<>'000000' group by code order by trade_count desc limit {$limit}";
		$result = $db->query($sql);
		break;
	case "down":
	case "up":
		$sql = "select * from stock where code<>'000000' order by c_percent ";
		
		if($type=="up") $sql .= "desc";
		else $sql .= "asc";
		
		$sql .= " limit {$limit}";
		$result = $db->query($sql);
		break;
	case "all":
		$sql = "select * from stock order by title asc limit {$limit}";
		$result = $db->query($sql);
		break;
	default:
		// 관련주(테마)
		$sql = "select * from stock where theme='{$type}' order by c_percent desc limit {$limit}";
		$result = $db->query($sql);
		break;
}
?>
<ul>
	<?php while($data=$result->fetch()){
	?>
	<li>
		<a href="/stock/info?code=<?php echo $data["code"]; ?>"><?php echo $data["title"]; ?></a>
		<div class="f_right"><?php 
		switch($type){
			case "heavy":
				echo num_to_han_s(str_replace(",","",number_format($data["price"]*$data["stock_count"])), 8);
				break;
			case "trade_price":
				echo num_to_han_s(str_replace(",","",number_format($data["trade_price"])), 8);
				break;
			case "trade_count":
				echo number_format($data["trade_count"])."주";
				break;
			default:
				$boxtype = "none";
				if($data["c_percent"] > 0){
					$boxtype = "up";
				}else if($data["c_percent"] < 0){
					$boxtype = "down";
				}
				echo "<span class='percent_box {$boxtype}box'>";
				if($boxtype=="up") echo "+";
				echo sprintf("%2.2f" ,round($data["c_percent"], 2))."%</span>";
				break;
		} ?></div>
	</li>
	<?php } ?>
</ul>
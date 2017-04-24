<?php
include_once "lib.php";
date_default_timezone_set('Asia/Seoul');

function get_last_day($month, $year) { 
    return date("t", mktime(0, 0, 0, $month, 1, $year));			
}	
function update_max(){
	global $db;
	$sql_update_stock = "update stock set max=price*1.3, min=price*0.7";
	$db->query($sql_update_stock);
	echo "\nAM -- 01:00 Max&Min Has Updated -- \n";
}
function update_y_price($yy, $mm, $dd){
	global $db;
	$sql = "select * from stock";
	$result=$db->query($sql);

	while($data=$result->fetch()){
		$selector = date("Y-m-d", strtotime("{$yy}-{$mm}-{$dd} -1 days"))." 23:00:00";
		$sql = "select * from canvas where code='{$data['code']}' and regdate='{$selector}' ";
		$data_y = $db->query($sql)->fetch();

		$sql = "select * from canvas where regdate='{$selector}' and code='{$data['code']}'";
		$data_c = $db->query($sql)->fetch();
		$sql = "update stock set y_price={$data_c['price']} where code='{$data['code']}'";
		$db->query($sql);
	}
	echo "y_price has updated \n";
}


$start_y = 2016;
$start_m = 7;
$start_d = 31;

$start_h = 1;


$end_y = 2016;
$end_m = 9;
$end_d = 16;

$end_h= 23;


for($y=$start_y;$y<=$end_y;$y++){
	$m = $y==$start_y ? $start_m : 1;
	$mto = $y==$end_y ? $end_m : 12;
	for($m;$m<=$mto;$m++){
		$mm = $m < 10 ? "0".$m : $m;

		$d = $y==$start_y && $m==$start_m ? $start_d : 1;
		$dto = $y==$end_y && $m==$end_m ? $end_d : get_last_day($m, $y);
			for($d;$d<=$dto;$d++){
			$dd = $d < 10 ? "0".$d : $d;

			$h = $y==$start_y && $m==$start_m && $d==$start_d ? $start_h : 1;
			$hto = $y==$end_y && $m==$end_m && $d==$end_d ? $end_h : 23;
				for($h;$h<=$hto;$h++){
					if($h==1){
						// 상,하한가 조정
						update_max();
						update_y_price($y, $mm, $dd);
					}
					$hh = $h < 10 ? "0".$h : $h;
					update_stock($y, $mm, $dd, $hh);
				}
			
		}
	}
}


function chance($num){ //  1/$num 확률로 true를 return
	if(rand(1,$num) == 1){
		return true;
	}else{
		return false;
	}
}
function update_stock($y, $m, $d, $h){
	global $db;
	$sql_stock = "select * from stock";
	$result_stock = $db->query($sql_stock);
	$total = 0;
	$total_trade = 0;
	

	while($data_stock=$result_stock->fetch()){
		if($data_stock["code"]!="000000"){
			$up = "-";
			$up_flag = false;
			$control = (rand(1001, $data_stock['range_new'])/1000)-1; // 0.001 ~ 0.03
			
			$trade_count = rand(30000, 300000);
			$output = "NORMAL";

			// 확률 조정(2%)
			$wave_1 = chance(50);
			$wave_2 = chance(150);
			if($wave_1){
				$control *= rand(2, 4);
				$trade_count *= rand(2, 3);
				$output = "!WAVED_1";
			}
			if($wave_2){
				$control *= rand(3, 7);
				$trade_count *= rand(3, 4);
				$output = "!WAVED_2";
			}
			// 확률 조정(2%)

			$control_won = $data_stock['price']*$control;

			if(rand(0,1)==0 && $data_stock["code"] != "001040"){
				$up = "+";
				$up_flag = true;
			}
			if($up_flag){
				if($data_stock['price'] + $control_won >= $data_stock['max']){
					// 98만 + 10만 // 상한가 : 100만
					$control_won = $data_stock['max'] - $data_stock['price'];
				}
			}else{
				if($data_stock['price'] - $control_won <= $data_stock['min']){
					// 51만 - 10만 // 하한가 : 50만
					$control_won = $data_stock['price'] - $data_stock['max'];
				}
			}
			// Add Canvas
			if($data_stock['mood']=="hold"){
				$control_won = 0;
			}
			$sql_canvas = "insert into canvas set code='{$data_stock['code']}', theme='{$data_stock['theme']}', price={$data_stock['price']}{$up}{$control_won}, trade_count={$trade_count}, regdate='{$y}-{$m}-{$d} {$h}:00:00', year={$y}, month={$m}, day={$d}, hour={$h}";



			$db->query($sql_canvas);
			$sql_update_stock = "update stock set price={$data_stock['price']}{$up}{$control_won}, c_percent=-((y_price-(price{$up}{$control_won})) / y_price * 100) where code='{$data_stock['code']}'";

			$db->query($sql_update_stock);
			// Add Canvas
			echo "[{$y}-{$m}-{$d} {$h}] Canvas&DB has updated [".$data_stock['code']."] [".$output."] ".$up.$control_won."\n";


			// 주가지수
			$total += $data_stock["price"]*$data_stock["stock_count"];
			$total_trade += $trade_count;
			// 거래량
		}

	}


	// 종합주가지수 업데이트
	$cospi = 0;
	$total_start_ori = 100000000000000; // 100조
	$total_start = $total_start_ori; // 100조
	

	// 증감분
	$total_start += (144397114*53081)/6.3966001301796; //  + 7.6조 (LG전자 증가분) 
	$total_start += (85600000*400000)/6.405722492991774; // + 34.24조 (넥슨 신규상장)
	$total_start += (265500*29116327)/6.197915891102241; // +7.7조 (CJ 신규상장)
	$total_start += (2166000*1421400)/6.042465559655449; // (롯데제과 신규상장)
	$total_start += (600000*5200000)/3.851201705447403; // (예수님을 닮아라  신규상장)
	// 기준치 : 108,300,248,052,457.2 (108.3조)


	// 증감분

	$cospi = $total / $total_start * 100;
	$cospi = round($cospi, 2);
	$sql_canvas = "insert into canvas set code='000000', theme='INDEX', price={$cospi}, ";

	
	$sql_canvas .= "trade_count={$total}, regdate='{$y}-{$m}-{$d} {$h}:00:00', year={$y}, month={$m}, day={$d}, hour={$h}";
	$db->query($sql_canvas);

	$sql_update_stock = "update stock set price={$cospi}, c_percent=-(((y_price-{$cospi})/{$cospi})*100) where code='000000'";
	$db->query($sql_update_stock);
	echo "total ".number_format($total)." WEB / COSPI : ".$cospi;
	// sleep(1);
	// 종합주가지수 업데이트

}

?>

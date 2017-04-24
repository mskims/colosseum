<?php
define("_ROOT", "{$_SERVER['DOCUMENT_ROOT']}");
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbinfo.php";
date_default_timezone_set('Asia/Seoul');

$get = isset($_GET["param"]) ? explode("/", $_GET["param"]) : NULL;

$dir = isset($get[0]) ? $get[0] : NULL;
$page = isset($get[1]) ? $get[1] : NULL;

$logined = isset($_SESSION["member"]) ? true : false; 
$admin = $logined && $_SESSION["member"]["id"] == "admin" ? true : false;


function sqlf($sql){
	global $db;
	return $db->query($sql)->fetch();
}
function sql($sql){
	global $db;
	return $db->query($sql);
}
function move($url){
	echo "<script>location.href='{$url}';</script>";
}
function msg($str){
	echo "<script>alert('{$str}');</script>";
}
function getStock($code){
	global $db;

	$y = date("Y");
	$m = date("m");
	$d = date("d");

	$h = date("H");

	$sql = "select * from stock where code='{$code}'";
	$data = $db->query($sql)->fetch();

	$s1["idx"] = $data["idx"];
	$s1["title"] = $data["title"];
	$s1["theme_e"] = $data["theme"];
	switch($s1["theme_e"]){
		case "IT":
			$s1["theme_k"] = "IT";
			break;
		case "ENT":
			$s1["theme_k"] = "엔터테인먼트";
			break;
		case "INDEX":
			$s1["theme_k"] = "종합주가지수";
			break;
		case "CAR":
			$s1["theme_k"] = "자동차";
			break;	
		case "SCH":
			$s1["theme_k"] = "학교";
			break;
		case "TRS":
			$s1["theme_k"] = "운송";
			break;
		case "MEDI":
			$s1["theme_k"] = "제약";
			break;
		case "ETC":
			$s1["theme_k"] =  "기타";
			break;
		case "FOOD":
			$s1["theme_k"] = "식품";
			break;
		default:
			$s1["theme_k"] = "기타";
			break;
	}
	$s1["price"] = $data["price"];
	$s1["code"] = $data["code"];
	if($code=="000000"){
		$sql = "select * from stock";
		$result_s = $db->query($sql);
		$total_c = 0;
		$total_p = 0;
		while($data_s=$result_s->fetch()){
			$total_c += $data_s["stock_count"];
			$total_p += $data_s["stock_count"] * $data_s["price"];
		}
		$s1["stock_count"] = $total_p/$s1["price"];
		$s1["stock_count2"] = $total_c;
	}else{
		$s1["stock_count"] = $data["stock_count"];
	}
	$s1["max"] = $data["max"];
	$s1["min"] = $data["min"];
	$s1["mood"] = $data["mood"];

	$s1["y_price"] = $data["y_price"];

//	$sql = "select sum(trade_count) as trade_count from canvas where year={$y} and month={$m} and day={$d} and code='{$code}'";
	$sql = "select sum(trade_count) as trade_count from canvas where year={$y} and month={$m} and day={$d} and code='{$code}'";
	$data_sum = $db->query($sql)->fetch();
	$s1["trade_count"] = $data_sum["trade_count"];
	
	$sql = "select max(price) as highest, min(price) as lowest from canvas where code='{$code}'";
	$data_an= $db->query($sql)->fetch();
	$s1["highest"] = $data_an["highest"];
	$s1["lowest"] = $data_an["lowest"];

	
	

	if($s1["y_price"] > $s1["price"]){
		$s1["c"] = "down";
		$s1["c_price"] = -($s1["price"] - $s1["y_price"]);
	}else if($s1["y_price"] < $s1["price"]){
		$s1["c"] = "up";
		$s1["c_price"] = ($s1["price"] - $s1["y_price"]);
	}else{
		$s1["c"] = "same";
		$s1["c_price"] = 0;
	}
	$s1["c_percent"] = $data["c_percent"];
	$s1["c_percent"] = round($s1["c_percent"], 2);

	
	$s1["sql"] = $sql;
	return $s1; 
}
function getYmd($date){
	$tmp = explode(" ", $date);
	return $tmp[0];
}
function getHis($date){
	$tmp = explode(" ", $date);
	return $tmp[1];
}
function getYmdType($date, $type, $shape){
	$tmp = getYmd($date);
	$tmp = explode("-", $tmp);
	switch($type){
		case "Ymd":
			$tmp = $tmp[0].$shape.$tmp[1].$shape.$tmp[2];
			break;
		case "Ym":
			$tmp = $tmp[0].$shape.$tmp[1];
			break;
		case "md":
			$tmp = $tmp[1].$shape.$tmp[2];
			break;
	}
	return $tmp;
}
function getHisType($date, $type, $shape){
	$tmp = getHis($date);
	$tmp = explode(":", $tmp);
	switch($type){
		case "His":
			$tmp = $tmp[0].$shape.$tmp[1].$shape.$tmp[2];
			break;
		case "Hi":
			$tmp = $tmp[0].$shape.$tmp[1];
			break;
		case "is":
			$tmp = $tmp[1].$shape.$tmp[2];
			break;
	}
	return $tmp;
}
function num_to_han_s($mny,$st=0){
	//숫자를 4단위로 한글 단위를 붙인다.
	//num_to_han_s('123456789') -> 1억2345만6789 
	//num_to_han_s('123456789',4) -> 1억2345만
	//num_to_han_s('123456789',6) -> 1억2345만 //무조건 4단위로 끊음
	$j2 = array("","만 ","억 ","조 ","경 "); // 단위의 한글발음 (조 다음으로 계속 추가 가능)
	 $arr=array();
	 $m=strlen($mny);
	 for($i=0;$i<$m;$i++){
	  $arr[]=$mny{$i};
	 }
	 $arr = array_reverse($arr);
	 $arrj1 = array();
	 $arrj2 = array();
	 for($i=0,$m=count($arr);$i<$m;$i++){
	//  $arrj1[] = $j1[$i%4]; 
	  $arrj2[] = $j2[floor($i/4)];
	 }
	 $cu = '';
	 $mstr = '';
	 $st = floor($st/4)*4;
	 for($i=$st,$m=count($arr);$i<$m;$i++){
	   $t = $arr[$i];
	   if($cu != $arrj2[$i]){
		$cu = $arrj2[$i];
		$t.=$cu;
	   }
	  $mstr = $t.$mstr;
	 }
	 return($mstr); 
}
function getMoneyUnit($val){
	$return = 0;
	if($val <= 10000) $return = 0;
	else if($val < 1000000) $return = 0;
	else if($val < 100000000) $return = 0;
	else if($val < 1000000000000) $return=4;
	else if($val < 1000000000000000) $return = 8;
	else $return = 12;
	return $return;
}
function echoBadge($bg, $cont){
	echo "<span class='percent_box' style='background: #{$bg}; '>{$cont}</span>";
}
function echoUpDown($num, $number_format, $up, $type, $shape){
	echo "<span class='";
	if($up){
		echo "up'>";
		if($shape){
			echo "▲ ";
		}else{
			echo "+ ";
		}
		if($number_format){
			echo number_format($num);	
		}else{
			echo $num;
		}
	}else{
		echo "down'>";
		if($shape){
			echo "▼ ";
		}else{
			echo "-	 ";
		}
		if($number_format){
			echo	number_format(-$num);	
		}else{
			echo	-$num;
		}
	}
	if($type!=NULL){
		echo " {$type}";
	}
	echo "</span>";
}
function time_elapsed_string($ptime){
	$etime = time() - $ptime;

    if ($etime < 1){
        return '방금 전';
    }
    $a = array( 365 * 24 * 60 * 60  =>  '년',
                 30 * 24 * 60 * 60  =>  '개월',
                      24 * 60 * 60  =>  '일',
                           60 * 60  =>  '시간',
                                60  =>  '분',
                                 1  =>  '초'
                );
    $a_plural = array( '년'   => '년',
                       '개월'  => '개월',
                       '일'    => '일',
                       '시간'   => '시간',
                       '분' => '분',
                       '초' => '초'
                );

	foreach ($a as $secs => $str){
		$d = $etime / $secs;
		if ($d >= 1){
			$r = round($d);
			return $r . '' . ($r > 1 ? $a_plural[$str] : $str) . ' 전';
		}
	}
}
?>
<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";


$code = $_POST["code"];
$duration = $_POST["duration"];
$size = $_POST["size"];
$height = $_POST["height"];
$durationtype = $_POST["durationtype"];
$title = isset($_POST["title"]) ? $_POST["title"] : "yes";
$total = isset($_POST["total"]) ? $_POST["total"] : "no";

$s1 = getStock($code);

?>

<a href="/stock/info?code=<?php echo $code ?>">
<?php if($title!="no"){ ?>
<!-- 타이틀 -->	
<div class="btitle">
	<!-- <a href="/stock/info?code=<?php echo $code ?>"><?php echo $s1["title"]; ?></a> -->
	<?php echo $s1["title"]; ?>
	<span class="point">
		<?php echo number_format($s1["price"]); ?> 웹 
		<span class="change_info <?php echo $s1["c"]; ?>">
			<?php if($s1["c"]=="up")echo "▲"; else echo "▼"; ?> <span class="price"><?php echo number_format($s1["c_price"]); ?> 웹</span>
			<span class="bar"></span>
			<?php 
				if($s1["c"]=="up"){
					echo "+ ";
				}
				echo sprintf("%2.2f" ,round($s1["c_percent"], 2)); ?>%
		</span>
	</span>

<!-- 시가총액 -->
<?php if($total!="no"){ ?>
	<div class="f_right" style="margin-right: 5px; ">시가총액 <?php 
		echo num_to_han_s(str_replace(",","",number_format($s1["price"]*$s1["stock_count"])), 8); 
//		echo str_replace(",", "", number_format($s1["price"]*$s1["stock_count"]));
	?> 웹</div>
<?php } ?>
<!-- 시가총액 -->
	<div class="badge">
		<?php
		if($code != "000000"){

		// 상한가
		if($s1["price"] == $s1["max"]){
			echoBadge("f00", "상한가");
		}
		// 상한가
		if($s1["price"] == $s1["min"]){
			echoBadge("00f", "하한가");
		}


		// 시총 클럽
		$club = $s1["price"]*$s1["stock_count"];
			if($club >= 1000000000000 ) {	// 1조 클럽
				if($club >= 10000000000000 ){ // 10조
					if($club >= 100000000000000){ // 100조
						echo "<span class='percent_box upbox'>100조</span>";
					}else{
						echo "<span class='percent_box upbox'>10조</span>";
					}
				}else{
					echo "<span class='percent_box upbox'>1조</span>";
				}
			}
		// 시총 클럽

		// 위험성알림
		$y3 = date("Y", strtotime("-3 days"));
		$m3 = date("m", strtotime("-3 days"));
		$d3 = date("d", strtotime("-3 days"));

		$y5 = date("Y", strtotime("-5 days"));
		$m5 = date("m", strtotime("-5 days"));
		$d5 = date("d", strtotime("-5 days"));

		$y15 = date("Y", strtotime("-15 days"));
		$m15 = date("m", strtotime("-15 days"));
		$d15 = date("d", strtotime("-15 days"));

		$y20 = date("Y", strtotime("-20 days"));
		$m20 = date("m", strtotime("-20 days"));
		$d20 = date("d", strtotime("-20 days"));

		$alert = NULL;
		$tmp = 0;
		$price = array();
		$days = ["3", "5", "15", "20"];
		$sql = "select * from canvas where code='{$code}' and (";
		$sql .= "
			(year={$y3} and month={$m3} and day={$d3} and hour=23) || 
			(year={$y5} and month={$m5} and day={$d5} and hour=23) || 
			(year={$y15} and month={$m15} and day={$d15} and hour=23) || 
			(year={$y20} and month={$m20} and day={$d20} and hour=23)";

		$sql .= ") order by regdate desc";
		$result_alert = $db->query($sql);
		while($data_alert=$result_alert->fetch()){
			$price[$days[$tmp]] = $data_alert["price"];
			$tmp++;
		}
		if($tmp > 0){
			if($s1["price"] >= $price["3"]  *1.15 || $s1["price"] <= $price["3"] * 0.85){ // 3일전 종가보다 15% 상승(하락) = 투자주의
				$alert = "주의";
			}
		}
		
		if($tmp > 2){
			if($s1["price"] >= $price["5"]  * 1.30 || $s1["price"] >= $price["15"] * 1.50 ){ // 5일전 종가보다 30% 상승 또는 15일전 종가보다 50% 상승 = 투자경고
				$alert = "경고";
			}
		}
		if($tmp > 3 ){
			if($s1["price"] >= $price["15"]  *1.70 || $s1["price"] >= $price["20"] * 2.00 || $s1["price"] <= $price["5"] * 0.70 ){ // 15일전 종가보다 70% 상승 또는 20일전 종가보다 100% 상승 또는 5일전 종가보다 30% 하락 = 투자위험
				$alert = "위험";
			}
		}

		if($tmp < 3){
			echoBadge("7e68cc", "신규상장");
		}

		$bg = "";
		switch($alert){
			case "주의":
				$bg = "f3c554";
				break;
			case "경고":
				$bg = "ff8d49";
				break;
			case "위험":
				$bg = "f73e50";
				break;
		}

		if($club <= 10000000000){ // 100억 미만 상폐주의
			if($club <= 1000000000){ // 10억 미만 상폐경고
				echoBadge("f00", "상장폐지경고");
			}else{
				echoBadge("f90", "상장폐지주의");
			}
		}
		if(isset($alert)){
			echoBadge($bg, "투자{$alert}");
		}
		// 위험성알림

		
		}
		?>
	</div>
</div>
<!-- 타이틀 -->
<?php } ?>

<!-- 차트 -->
<div class="chart_area">
	<div id="cvs_<?php echo $code; ?>" style="width: <?php echo $size; ?>px; height: <?php echo $height; ?>px; position: relative;"></div>
	<!--<canvas width="<?php echo $size; ?>" height="<?php echo $height; ?>" id="chart_<?php echo $code; ?>"></canvas>-->
</div>
</a>
<!-- 차트 -->


<?php
// $date_selector = date("Y-m-d", strtotime("-{$duration} {$durationtype}"));
// $sql = "select * from canvas where code='000000' and (day=1 or day=7 or day=14 or day=21 or day=28) and hour=1 ";
$sql = "select * from(select * FROM canvas where code='{$code}' ";
switch($durationtype){
	case "month":
		$selector = $duration;
		$sql .= "and hour=1 and day=1 order by idx desc limit {$selector}";
		break;
	case "week":
		$selector = $duration;
		$sql .= "and hour=23 and (day=1 or day=7 or day=14 or day=21 or day=28) order by idx desc limit {$selector}";
		break;
	case "day":
		$selector = $duration;
		$sql .= "and hour=23 order by idx desc limit {$selector}";
		break;
	case "3days":
		$selector = $duration;
		$sql .= "and hour=23 and (day=1 or day=4 or day=7 or day=10 or day=13 or day=16 or day=19 or day=22 or day=25 or day=28 or day=31 ) order by idx desc limit {$selector}";
		break;
	case "3hour":
		$selector = $duration;
		$sql .= "and (hour=1 or hour=4 or hour=7 or hour=8 or hour=11 or hour=14 or hour=17 or hour=20 or hour=23) order by idx desc limit {$selector}";
		break;
	case "hour":
		$selector = $duration;
		$sql .= "order by idx desc limit {$selector}";
		break;
}
$sql .= " ) tmp order by idx asc";
$result = $db->query($sql);
$labels = [];

$max = 0;
?>
<script type="text/javascript">
/*
$(function(){
	var code = "<?php echo $code; ?>";
	var size = <?php echo $size; ?>;

	var data = {
		datasets: [
			{
				label: code,
				fillColor: "rgba(151,187,205,0.2)",
				strokeColor: "rgba(151,187,205,1)",
				pointColor: "rgba(151,187,205,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(151,187,205,1)",
				data: [<?php /*
					$idx = 0;
					while($data=$result->fetch()){
						$idx++;
						if($data["price"] > $max) $max = $data["price"];
						if(!isset($min)) $min = $data["price"];
						else if($data["price"] < $min) $min = $data["price"];

						echo $data["price"].",";
						if($idx%2==0){
//							$data["regdate"] = "";
						}
						array_push($labels, $data["regdate"]);
					}
					if($durationtype!="hour"){
						echo $s1["price"].",";
						array_push($labels, date("Y-m-d H:i:s"));
					}
				*/ ?>]
			}
		],
	};
	data.labels = [<?php /*
	foreach($labels as $label){
		echo "'";
		if($label != ""){
			switch($durationtype){
				case "month":
					echo getYmdType($label, "Ym", "-");
					break;
				case "week":
					echo getYmdType($label, "md", "/");
					break;
				case "day":
					echo getYmdType($label, "md", "/");
					break;
				case "3days":
					echo getYmdType($label, "md", "/");
					break;
				case "hour":
					echo getHisType($label, "Hi", ":");
			}
		}
		echo "',";
	}*/
	?>];

//		Chart.defaults.global.scaleShowLabels = false;
	Chart.defaults.global.scaleLabel = "<%=parseInt(value).toLocaleString()%>";

	var ctx = document.getElementById("chart_"+code).getContext("2d");

	var step = <?php echo $max / 20; ?>;
	var max = <?php echo $max; ?>;
	var start = <?php echo $min; ?>;

	var myLineChart = new Chart(ctx).Line(data,{
		skipLabels: true,
		showTooltips: true,
		pointHitDetectionRadius : 0,
//			bezierCurve: false,
		scaleLabel: "<%=parseInt(value).toLocaleString()%>",
		tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value.toLocaleString() %>",
		
		scaleIntegersOnly: true,
		scaleOverride: true,
		scaleSteps: Math.ceil((max-start)/step),
		scaleStepWidth: step,
		scaleStartValue: start
	});
});*/
</script>
<script src="/_chart/chart24.js"></script>
<script type="text/javascript">
$(function(){

var data = [<?php 
while($data=$result->fetch()){
	echo $data['price'].",";
}
?>];
chart24.init("cvs_<?php echo $code; ?>", data);

});
</script>

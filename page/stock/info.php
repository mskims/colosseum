<?php
if(isset($_GET["code"])){
	$code = $_GET["code"];
	$showChart = isset($_GET["chart"]) ? $_GET["chart"] : 1;
	$boardAction = isset($_GET["action"]) ? $_GET["action"] : "list";
	$boardIdx = isset($_GET["idx"]) ? $_GET["idx"] : 0;

	$s1 = getStock($code);

	if($logined){
		// 매도 PLACEHOLDER 
		$my_count = sqlf("select sum(count) as cnt from member_stock where code='{$code}' and id='{$_SESSION['member']['id']}' ");
		if($my_count["cnt"] == NULL){
			$my_count["cnt"] = 0;
		}
		// 매도 PLACEHOLDER 
	}

	
}else{
	echo "<script>location.href='/stock';</script>";
	exit();
}
?>

<div class="box_wrap c2 pr0">
	<div class="box">
		전일 <div class="f_right"><?php echo number_format($s1["y_price"]); ?> 웹</div>
	</div>
</div>
<?php if($code!="000000"){ ?>
<div class="box_wrap c2 pr0">
	<div class="box">
		거래량 <div class="f_right"><?php echo number_format($s1["trade_count"]); ?> 주</div>
	</div>
</div>

<div class="box_wrap c2 pr0">
	<div class="box">
		상한가 <div class="f_right up"><?php echo number_format($s1["max"]); ?> 웹</div>
	</div>
</div>
<div class="box_wrap c2 pr0">
	<div class="box">
		하한가 <div class="f_right down"><?php echo number_format($s1["min"]); ?> 웹</div>
	</div>
</div>
<?php } ?>
<div class="box_wrap c<?php if($code=="000000") echo 10; else echo 4; ?> chart_chg">
	<div class="box">
		차트 종류
		<div class="f_right" data-code="<?php echo $code; ?>">
			<a href="javascript:;" class="active" data-durationtype="hour" data-duration="23">1일</a>
			<a href="javascript:;" data-durationtype="3hour" data-duration="63">1주</a>
			<a href="javascript:;" data-durationtype="day" data-duration="30">1개월</a>
			<a href="javascript:;" data-durationtype="3days" data-duration="60">3개월</a>
			<a href="javascript:;" data-durationtype="week" data-duration="50">1년</a>
			<a href="javascript:;" data-durationtype="month" data-duration="36">3년</a>
		</div>
	</div>
</div>


<div class="box_wrap c12 pt0">
	<div class="box">
		<?php if(true){ ?> 
		<div>
			<div class="chart" data-code="<?php echo $code; ?>" data-durationtype="hour" data-duration="23" data-total="yes" data-height="300"></div>
		</div>
		<?php } ?>
	</div>
	
</div>
<div class="box_wrap c2d5">
	<div class="box">
		상장주식수 <div class="f_right"><?php if($code=="000000") $s1["stock_count"]=$s1["stock_count2"]; echo number_format($s1["stock_count"]); ?> 주</div>
	</div>
</div>
<div class="box_wrap c2">
	<div class="box">
		최고 <div class="f_right up"><?php echo number_format($s1["highest"]); ?> 웹</div>
	</div>
</div>
<div class="box_wrap c2">
	<div class="box">
		최저 <div class="f_right down"><?php echo number_format($s1["lowest"]); ?> 웹</div>
	</div>
</div>


<div class="box_wrap c1d25 pr0">
	<input class="info_trade_input al_r" type="text" id="buy_count" name="buy_count" placeholder="수량입력" />
</div>
<div class="box_wrap c0d75 pl0 pr0">
	<button type="button" class="info_trade_button bl0" id="buy_button" style="height: 38px; ">매수</button>
</div>
<div class="box_wrap c0d75 pl0 ">
	<button type="button" class="info_trade_button bl0" id="buy_max_button" style="height: 38px; " onclick="<?=$logined?"$('#buy_count').val(".floor($_SESSION['member']['balance'] / $s1['price']).");":""?>">MAX</button>
</div>
<div class="box_wrap c1d25 pr0 pl0">
	<input class="info_trade_input al_r" type="text" id="sell_count" name="sell_count" placeholder="<?php if($logined) echo $my_count["cnt"]."주"; ?>" />
</div>
<div class="box_wrap c0d75 pl0 pr0">
	<button type="button" class="info_trade_button bl0" id="sell_button" style="height: 38px; ">매도</button>
</div>
<div class="box_wrap c0d75 pl0">
	<button type="button" class="info_trade_button bl0" id="sell_max_button" style="height: 38px; " onclick="<?=$logined?"$('#sell_count').val({$my_count["cnt"]});":""?>">MAX</button>
</div>

<!-- <div class="box_wrap c2">
	<div class="box">
		매도
	</div>
</div> -->


<div class="c8 stock_info_cont f_left">

<div class="box_wrap c12">
	<div class="box">
		<div class="btitle tabs">
			<a href="?code=<?=$code?>&type=news" class="<?=$boardIdx==null ? "active" : ""?>">실시간 뉴스</a>
			<a href="?code=<?=$code?>&type=talk" class="<?=$boardIdx!=null ? "active" : "" ?>">종목 토론실</a>
		</div>

		<div class="stock-content">
		<div class="stock-content-wrap" style="<?=$boardIdx!=null ? "margin-left: -100%; " : ""?>">
		<!-- 뉴스 -->
		<div class="news">
		<?php include_once _ROOT."/page/stock/news.php" ;?>
		</div>
		<!-- 뉴스END -->


		<!-- 종목 토론실 -->
		<div class="talk">
		<?php
		include_once _ROOT."/page/stock/talk.php";
		?>
		</div>
		<!-- 종목 토론실END -->
		</div>
		</div>

	</div>
</div>

</div>



<div class="c4 stock_info_aside f_left">

<div class="box_wrap c12">
	<div class="box">
		<div class="btitle"><?php echo $s1["theme_k"]; ?> 관련주</div>
		<div>
			<div class="list" data-type="<?php echo $s1["theme_e"]; ?>" data-limit="10"></div>
		</div>
	</div>
</div>
<div class="box_wrap c12">
	<div class="box">
		<div class="btitle">대주주</div>
		<div>
			<div class="list" data-listtype="2" data-type="owner" data-limit="<?php echo $code; ?>"></div>
		</div>
	</div>
</div>
<div class="box_wrap c12">
	<div class="box">
		<div class="btitle">실시간 급상승</div>
		<div>
			<div class="list" data-type="up" data-limit="10"></div>
		</div>
	</div>
</div>


</div>


<script type="text/javascript">
$(function(){
<?php if($logined){ ?>


	var count, code, msg, success=false;
	$("#buy_button").click(function(){
		code = "<?php echo $code; ?>";
		count = $("#buy_count").val();

		success = false;
		if(count.length > 0){
			$.ajax({
				url: "/ajax/stock_buy.php",
				type: "POST",
				data: {"code": code, "count": count},
				success: function(data){
					data = data.split("/");
					switch(data[0]){
						case "01":
							msg = "올바른 수량을 넣어주세요";
							break;
						case "02":
							msg = "잔액이 부족합니다";
							break;
						case "03":
							msg = "구매할 수 있는 주식이 없습니다 / 남은 수량 : " + data[1] + " 주";
							break;
						case "99":
							msg = "매수되었습니다";
							success = true;
							break;
						default: 
							msg = "알수없는 오류";
							break;
					}
					
					if(success){
						load_asset("<?php echo $_SESSION['member']['id']; ?>");
					}
					alert(msg);
					$("#buy_count").val("");
				}
			});

		}else{
			alert("수량을 입력해주세요.");
		}
	});


	var my_count = <?=$my_count['cnt']?>;
	$("#sell_button").click(function(){
		code = "<?php echo $code; ?>";
		count = $("#sell_count").val();

		success = false;
		if(count.length > 0){
			$.ajax({
				url: "/ajax/stock_sell.php",
				type: "POST",
				data: {"code": code, "count": count},
				success: function(data){
					data = data.split("/");
					switch(data[0]){
						case "01":
							msg = "올바른 수량을 넣어주세요";
							break;
						case "03":
							msg = "보유 주식이 부족합니다 / 보유 : " + data[1] + " 주";
							break;
						case "99":
							msg = "매도되었습니다";
							success = true;
							break;
						default: 
							msg = "알수없는 오류";
							break;
					}
					
					if(success){
						load_asset("<?php echo $_SESSION['member']['id']; ?>");
						my_count -= count;
						$("#sell_count").val("").attr("placeholder", my_count+"주");
					}
					alert(msg);
				}
			});

		}else{
			alert("수량을 입력해주세요.");
		}
	});
<?php } else { ?>
	$("#buy_button").click(function(){
		alert("로그인해주세요");
		location.href="/stock";
	});
<?php } ?>

	$(".tabs a").click(function(e){
		e.preventDefault();
		$(".tabs a").removeClass("active");
		$(this).addClass("active");
		var action = $(this).attr("href").split("=")[2];
		console.log(action);
		if(action == "news"){
			$(".stock-content-wrap").stop().animate({"margin-left": "-0%"});
		}else 	if(action == "talk"){
			$(".stock-content-wrap").stop().animate({"margin-left": "-100%"});
			// $(".talk .layout-list").html('<table><colgroup><col style="width: 50%; " /><col style="width: 15%; " /><col style="width: 10%; " /><col style="width: 10%; " /><col style="width: 15%; " /></colgroup><thead><tr><th>제목</th><th>작성자</th><th>투자의견</th><th>추천</th><th>날짜</th></tr></thead><tbody></tbody></table>');
			$(".talk .layout-list").show();
			$(".talk .layout-list tbody").html("");
			load_board(<?=$s1["idx"]?>, "list", ".talk tbody");
		}
	});
});
</script>
<?php
if(!$logined){
	msg("로그인해주세요");
	move("/stock");
}

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
?>
<style type="text/css">
.mystock { width: 100%; font-size: 1em; line-height: 50px; }
	.mystock table { border-collapse: collapse; width: 100%; }
	.mystock td { padding: 0 0px; padding-left: 10px; box-sizing: content-box; }
	.mystock td:first-child { padding-left: 0; }

	.mystock tr:not(:last-child) { border-bottom: 1px solid #f1f1f1; }
.p_side { padding: 0 10px; }

.user-info-header .box { height: 41px; }
</style>


<!-- Userinfo -->
<div class="user-info-header">
	<div class="box_wrap c1d25 pb0 pr0 al_c">
		<div class="box">
			투자금액
		</div>
	</div>
	<div class="box_wrap c3d5 pb0 pr0">
		<div class="box">
			<div class="f_right" id="ori_sum"></div>
		</div>
	</div>
	<div class="box_wrap c1d25 pb0 pr0 al_c">
		<div class="box">
			평가잔액
		</div>
	</div>
	<div class="box_wrap c3d5 pb0 pr0">
		<div class="box">
			<div class="f_right" id="now_sum"></div>
		</div>
	</div>
	<div class="box_wrap c0d75 pb0 pr0 al_c">
		<div class="box" style="padding: 10px 0; ">
			수익률
		</div>
	</div>
	<div class="box_wrap c1d75 pb0" >
		<div class="box" style="padding: 10px 5px 10px 0; ">
			<div class="f_right" id="ratio"></div>
		</div>
	</div>
</div>


<div class="box_wrap c3 pb0 pr0">
	<div class="box">
		<div class="p_side">
			종목명 (비중)
		</div>
	</div>
</div>

<div class="box_wrap c1d5 pb0 pr0 al_c">
	<div class="box">
		<div class="">
			현재가
		</div>
	</div>
</div>
<div class="box_wrap c1d5 pb0 pr0 al_c">
	<div class="box">
		<div class="">
			평균단가
		</div>
	</div>
</div>
<div class="box_wrap c2 pb0 pr0 al_c">
	<div class="box">
		<div class="">
			손익
		</div>
	</div>
</div>
<div class="box_wrap c1d5 pb0 pr0 al_c">
	<div class="box">
		<div class="">
			손익률
		</div>
	</div>
</div>

<div class="box_wrap c1d5 pb0 pr0 al_c">
	<div class="box">
		<div class="">
			보유
		</div>
	</div>
</div>
<div class="box_wrap c1 pb0 al_c">
	<div class="box">
		<div class="">
			매도
		</div>
	</div>
</div>

<div class="box_wrap c12 mystock">
	<div class="box">
	<div>
		<table>
			<colgroup>
				<col style="width: 24%; " />
				<col style="width: 13%; " />
				<col style="width: 13%; " />
				<col style="width: 17%; " />
				<col style="width: 13.5%; " />
				<col style="width: 13%; " />
				<col style="width: 6.5%; " />
			</colgroup>
			<?php
			$ori_sum = 0;
			$now_sum = 0;
			while($data_sd=$result_sd->fetch()){
			$ori_sum += $data_sd["avg_price"] * $data_sd["sum_count"];
			$now_sum += $data_sd["price"] * $data_sd["sum_count"];
			?>
			<tr class="my_stock_tr" id="tr_<?php echo $data_sd["code"]; ?>">
				<td class="dn">
					<input type="hidden" class="avg_price_sum" value="<?php echo $data_sd["avg_price"]*$data_sd["sum_count"]; ?>" />
				</td>
				<td>
					<a href="/stock/info?code=<?php echo $data_sd["code"]; ?>">
						<?php echo $data_sd["title"]; ?>
						<span class="weight small" id="w_<?php echo $data_sd["code"]; ?>"></span>
						<?php // echo $data_sd["code"]; ?>
					</a>	
				</td>
				<td class="al_r"><?php echo number_format($data_sd["price"]); ?> 웹</td>
				<td class="al_r"><?php echo number_format($data_sd["avg_price"]); ?> 웹</td>
				<td class="al_r"><?php
				// 손익 계산
				$ori = $data_sd["avg_price"]*$data_sd["sum_count"];
				$now = $data_sd["price"]*$data_sd["sum_count"];
				$gain = $now - $ori;
				$up = $gain > 0 ? true : false;
				echo echoUpDown($gain, true, $up, " 웹", true);
				?></td>
				<td class="al_r"><?php
					$gain_percent = $gain / $ori * 100;
					echo echoUpDown(round($gain_percent, 2), false, $up, "%", false);
				?></td>
				<td class="al_r"><?php echo number_format($data_sd["sum_count"]); ?> 주</td>
				<td class="al_c">
					<a href="/stock/info?code=<?php echo $data_sd["code"]; ?>" class="link">매도</a>
				</td>
			</tr>
<!-- 			<tr>
				<td colspan="7">
					<div class="chart" data-code="<?php echo $data_sd["code"]; ?>" data-durationtype="hour" data-duration="23" data-total="yes"  data-title="no"></div>
				</td>
			</tr> -->
			<?php } ?>
		</table>
	</div>
</div>
<script>
$(function(){
	var ori_sum = <?=$ori_sum?>;
	var now_sum = <?=$now_sum?>;
	var height = 0;
	var now_height = 0;
	$("#ori_sum").html(writeNumber("<?=number_format($ori_sum)?>", "<?=$now_sum>$ori_sum ? 'up' : 'down'?>", 1));
	$("#now_sum").html(writeNumber("<?=number_format($now_sum)?>", "<?=$now_sum>$ori_sum ? 'up' : 'down'?>", 1));

	$("#ratio").html(writeNumber("<?=$now_sum>$ori_sum ? '+' : ''?>"+((now_sum - ori_sum)/ ori_sum * 100).toFixed(2)+"%", "<?=$now_sum>$ori_sum ? 'up' : 'down'?>", 1));

	$(".my_stock_tr").each(function(){
		now_height = $(this).find(".avg_price_sum").val() / ori_sum * 100
		height += now_height;
//		alert("비중: "+now_height+" %");
		$(this).find(".weight").html("["+now_height.toFixed(2)+"%]");
	});

});
function writeNumber(num, color, size){
	var html = "";
	num += "";
	for(var i = 0 ; i<num.length ; i++){
		console.log(num[i]);
		html += "<span class='sprite sprite_num sprite_num_size"+size+" ";
		switch(num[i]){
			case "+":
				html += "sprite_shape plus";
				break;
			case "-":
				html += "sprite_shape minus";
				break;
			case ".":
				html += "dot";
				break;
			case ",":
				html += "comma";
				break;
			case "%":
				html += "percent";
				break;
			default: 
				html += "no"+num[i];
				break;
		}
		html += " no_"+color+"'>";
		html += num[i]+"</span>";
	}
	return html;
}
</script>
<?php
$key = $_GET["search_key"];

$sql = "
	SELECT
		*
	FROM
		stock
	WHERE
		code LIKE '%{$key}%'
	OR
		title LIKE '%{$key}%'
	OR
		theme LIKE '%{$key}%'
	ORDER BY
		price*stock_count
	DESC
";
$res = sql($sql);
?>



<style type="text/css">
.search_result .box_wrap { opacity: 0; top: -50px; transition: top 1s, opacity 1s; }
</style>
<div class="search_result">
<?php while($data=$res->fetch()){ ?>
<div class="box_wrap c12">
<div class="box">
	<div>
		<div class="chart" data-code="<?=$data["code"]?>" data-durationtype="day" data-duration="23" data-total="yes" data-height="200"></div>
	</div>
</div>
</div>
<?php } ?>
</div>




<script type="text/javascript">
// Search Box Sliding Script
$(function(){
	$(".search_result .box_wrap").each(function(index){
//		setTimeout(function(){
			$(this).css({
				"opacity": 1,
				"top": 0,
			});	
//		}, 500);
	});
});
</script>
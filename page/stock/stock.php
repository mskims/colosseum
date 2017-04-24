<?php
$sql = "select code from stock where code<>'000000' order by rand() limit 4";
$random = $db->query($sql)->fetchAll();
?>
<div class="fade out top">


<?php if($logined){ ?>
<div class="box_wrap c12 pb0">
	<div class="box">
		<?php echo $_SESSION["member"]["nick"]; ?> 님 환영합니다. 오늘도 성투하세요. <a href="/member/logout" class="link">로그아웃</a>
	</div>
</div>
<?php } ?>
<div class="box_wrap c12">
	<div class="box">
		<div class="btitle">TIP</div>
		<div class="clear">
			종목 이름을 클릭해 상세 정보를 확인하세요
		</div>
	</div>
</div>
<div class="box_wrap c12">
	<div class="box">
		<div>
			<div class="chart" data-code="000000" data-durationtype="day" data-duration="23" data-total="yes" data-height="200" data-chart="no"></div>
		</div>
	</div>
</div>
<div class="box_wrap c12">
	<div class="box">
		<div>
			<div class="chart" data-code="<?=$random[0]["code"]?>" data-durationtype="hour" data-duration="23" data-total="yes"></div>
		</div>
	</div>
</div>
<div class="box_wrap c4">
	<div class="box">
		<div class="btitle">시가총액 상위</div>
		<div>
			<div class="list" data-type="heavy" data-limit="10"></div>
		</div>
	</div>
</div>
<div class="box_wrap c4">
	<div class="box">
		<div class="btitle">실시간 급상승</div>
		<div>
			<div class="list" data-type="up" data-limit="10"></div>
		</div>
	</div>
</div>
<div class="box_wrap c4">
	<div class="box">
		<div class="btitle">실시간 급하락</div>
		<div>
			<div class="list" data-type="down" data-limit="10"></div>
		</div>
	</div>
</div>



<div class="">
	<div class="left c8 f_left">


		<div class="box_wrap c12">
			<div class="box">
				<div>
					<div class="chart" data-code="<?=$random[1]["code"]?>" data-durationtype="hour" data-duration="23" data-total="yes"></div>
				</div>
			</div>
		</div>

		<div class="box_wrap c12">
			<div class="box">
				<div>
					<div class="chart" data-code="<?=$random[2]["code"]?>" data-durationtype="hour" data-duration="23" data-total="yes"></div>
				</div>
			</div>
		</div>

		<div class="box_wrap c12">
			<div class="box">
				<div>
					<div class="chart" data-code="<?=$random[3]["code"]?>" data-durationtype="hour" data-duration="23" data-total="yes"></div>
				</div>
			</div>
		</div>

	</div>
	<div class="right c4 f_left">
		<div class="box_wrap c12">
			<div class="box">
				<div class="btitle">전체</div>
				<div>
					<div class="list" data-type="all" data-limit="0"></div>
				</div>
			</div>
		</div>
	</div>
</div>


</div>




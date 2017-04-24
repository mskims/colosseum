<link rel="stylesheet" href="/common/css/sub.css" />


<div class="sub">
		<?php
		$path = _ROOT."/page/index.php";
		if(!empty($dir) && empty($page)){
			$p = $dir;
			$path = _ROOT."/page/{$dir}/{$dir}.php";
		}else if(!empty($page)){
			$p = $page;
			$path = _ROOT."/page/{$dir}/{$page}.php";
		}else{
			$p = "404";
		}
		?>

	<div class='title'><div class='wh_c'><?php 
	$sql = "select * from menu where m_key = '{$p}' ";
	$title = $db->query($sql)->fetch();
	$title = $title["text"];
	echo $title;
	?><div class="pnav"><?php
	echo $pnav;
	?></div></div></div>
	<div class="cont scont">
		<div>
		<div class="wh_c clear">


		<div class="fade out top">
			<div class="popup_wrap">

		<?php
		if(file_exists($path)){ 
			echo "\t";

			if($logined){ ?>

				<!-- ASSET POPUP -->
				<div class="box_wrap c12 popup lh30 small">
					<div class="box">
						<div class="btitle">보유 자산</div>
						<div class="clear">
							<div class="c4 al_c f_left asset">수익률</div><div class="c8 al_r pr10 f_left" id="asset_ratio">0.00%</div>
							<div class="c4 al_c f_left asset">주식</div><div class="c8 al_r pr10 f_left" id="asset_stock">0</div>
							<div class="c4 al_c f_left asset">현금</div><div class="c8 al_r pr10 f_left" id="asset_balance">0</div>
							<div class="c4 al_c f_left asset">합계</div><div class="c8 al_r pr10 f_left" id="asset_sum">0</div>
						</div>
					</div>
				</div>


				<!-- LOGOUT POPUP -->
				<div class="box_wrap c12 pt0">
					<button type="button" class="info_trade_button" onclick="location.href='/member/logout';">로그아웃</button>
				</div>

			
			<script type="text/javascript">
				load_asset("<?php echo $_SESSION['member']['id']; ?>");
			</script>
			
			<?php } else { ?>

			<div class="box_wrap c12 popup lh30 small">
				<div class="box">
					<div class="btitle">TIP</div>
					<div class="clear">
						종목 이름을 클릭해 상세 정보를 확인하세요
					</div>
				</div>
			</div>


			<!-- LOGIN POPUP -->
			<div class="box_wrap c12 pt0">
				<button type="button" class="info_trade_button" onclick="location.href='/member/login';">로그인</button>
			</div>
			<?php } ?>


			</div>
			</div>
			<?php
			include_once($path);
				}else{
					echo "</div></div><h3>존재하지 않는 페이지입니다</h3>";
				}
			?>
		</div>
		</div>
	</div>
</div>
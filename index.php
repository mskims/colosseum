<?php
session_start();
date_default_timezone_set('Asia/Seoul');
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="EditPlus®">
<meta name="viewport" content="width=device-width">
<link rel="stylesheet" href="/common/css/font.css" />
<link rel="stylesheet" href="/common/css/common.css" />
<link rel="stylesheet" href="/common/css/sprite.css" />

<link rel="shortcut icon" href="/favi.ico">

<script src="/common/js/jquery.js"></script>
<script src="/common/js/common.js"></script>
<title>Colosseum Beta</title>
</head>
<body>
<div class="wrap">
<?php
$sql = "select * from menu where parent='me' and hidden=0 order by soon_su asc";
$result_m1 = $db->query($sql);

$pnav = "<a href='/'>홈</a> > ";
?>
<div class="gnb <?php if(empty($dir)) echo "gnb_main"; ?>">
	<div class="wh_c">
		<ul class='main_m'>
			<li><a href="/">홈</a></li>
			<?php
			while($data_m1=$result_m1->fetch()){ 
				if($data_m1["m_key"]==$dir) $pnav .= "<a href='/{$data_m1['m_key']}'>".$data_m1["text"]."</a>";
				?>
<li class="gnb_<?=$data_m1["m_key"]?>">
				<a href="/<?php echo $data_m1["m_key"]?>"><?php echo $data_m1["text"]; ?></a>
<?php 
$sql = "select * from menu where parent='{$data_m1['m_key']}' order by soon_su asc";
$result_m2 = $db->query($sql);
?>
				<ul class='sub_m'>
<?php
while($data_m2=$result_m2->fetch()){
	if($data_m2["m_key"]==$page){
		$pnav .= " > <a href='/{$data_m1['m_key']}/{$data_m2['m_key']}";
		if($data_m2["m_key"]=="info"){
			$pnav .= "?code=".$_GET["code"];
		}
		$pnav .= "'>".$data_m2["text"]."</a>";
	}

if($data_m2["hidden"]==0){
?>
					<li><a href="/<?php echo $data_m1["m_key"]; ?>/<?php echo $data_m2["m_key"]?>"><?php echo $data_m2["text"]; ?></a></li>
<?php } 
}
?>
				</ul>
			</li>
			<?php }	?>

		</ul>
	</div>
</div>

<?php
if(!empty($dir)){
	include_once _ROOT."/page/sub.php";
}else{
	include_once _ROOT."/page/index.php";
}
?>
<div class="footer">
	<div class="wh_c">
		<div class="web-s">
			<img src="/static/img/html5.png" alt="" />
			<img src="/static/img/css3.png" alt="" />
		</div>
		<div class="info">
			Copyright (C) Colosseum All rights reserved
		</div>
	</div>
</div>


</div>

<script type="text/javascript">
$(function(){
	$(".fade").attr("class", "fade");
//	$(".fade").removeClass("out");

	$(".gnb_search").append('<div class="search dn"><form action="/search" method="get"><input type="text" name="search_key" placeholder="코드, 종목 검색" id="search_key"/><button type="submit"></button></form></div>');
	$(".gnb_search a").click(function(e){
		e.preventDefault();
		$(this).hide();
		$(".gnb_search .search").show();
		$("#search_key").focus();
	});
	$(".search form").submit(function(e){
		if($("#search_key").val().length < 1){
			alert("검색어를 입력해주세요");
			return false;
		}
	});
});
</script>
</body>
</html>


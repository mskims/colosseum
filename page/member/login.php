<?php
if(isset($_POST["id"])){
	$_POST["pass"] = md5($_POST["pass"]);

	$sql = "select * from member where id='{$_POST['id']}' and pass='{$_POST['pass']}'";
	$res = $db->query($sql)->fetch() or die("<script>alert('아이디 또는 암호가 다릅니다');location.href='/member/login';</script>");

	$_SESSION["member"] = $res;

	move("/stock");
}else{ ?>


<div class="login">
	<div class="box_wrap c12">
		<div class="box clear">
			<h2 class="al_c">로그인</h2>
			<!-- Login Form -->
			<form action="/member/login" method="post">
				<div class="box_wrap c12">
					<input class="info_trade_input" type="text" name="id" placeholder="아이디" />
				</div>
				<div class="box_wrap c12">
					<input class="info_trade_input" type="password" name="pass" placeholder="비밀번호" />
				</div>
				<div class="box_wrap c12">
					<button type="submit" class="info_trade_button">로그인</button>
				</div>
			</form>
			<!-- Login Form -->
		</div>
	</div>
	<div class="box_wrap c12">
		<div class="box clear">
			<div class="btitle">테스트계정</div>
			<div class="clear">
				admin / 1234<br>
				test1 / 1234<br>
				test2 / 1234<br>
				test3 / 1234<br>
				test4 / 1234<br>
				test5 / 1234
			</div>
		</div>
	</div>
</div>
<?php } ?>
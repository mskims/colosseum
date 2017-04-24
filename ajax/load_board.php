<?php

include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";


$code = isset($_GET["code"]) ? $_GET["code"] : NULL;
$idx = isset($_GET["idx"]) ? $_GET["idx"] : NULL;
$type = isset($_GET["type"]) ? $_GET["type"] : "list";
$arr = [];

if($code!=NULL){
	$col = "board.idx, board.subject, member.nick, board.opinion, (select count(*) from recommend where recommend.idx_b = board.idx) as recommend, board.regdate";
	if($type=="list"){
		$where = "board.code={$code}";
		
	}else if($type="read"){
		$where = "board.idx={$idx}";
		$col .= ", board.content, board.hit";
	}
	$sql = "
	SELECT 
		{$col}
	FROM board
	JOIN member
	ON board.id = member.idx
	WHERE
		{$where}
	ORDER BY
		board.idx DESC
	";
	$res = sql($sql);



	if($type=="list"){
		$boardCount = 0;
		while($data=$res->fetch()){
			$boardCount++;
			$ito = count($data)/2-1;
			for($i=0;$i<=$ito;$i++){
				unset($data[$i]);
			}
			$data["regdate"] = time_elapsed_string(strtotime($data["regdate"]));
			array_push($arr, $data);
		}
		if($boardCount==0){
			error("02");
		}
	}else if($type == "read"){
		$data = $res->fetch();
		$ito = count($data)/2-1;
		for($i=0;$i<=$ito;$i++){
			unset($data[$i]);
		}
		$data["regdate"] = time_elapsed_string(strtotime($data["regdate"]));
		array_push($arr, $data);
	}
}else{
	error("01");
}
function error($ecode){
	global $arr;
	$arr["error"] = $ecode;
}

echo json_encode($arr,JSON_UNESCAPED_UNICODE);
?>
var idx = 0;
var last_list;
var url;
var code,duration,size,durationtype,total,height;
var type, limit;

$(function(){
	// 차트 로드
	load_chart("all");

	$(".chart_chg a").on("click", function(){
		$(".chart_chg a").removeClass("active");
		$(this).addClass("active");
		durationtype = $(this).data("durationtype");
		duration = $(this).data("duration");
		code = $(this).parent().data("code");
		$("#area_"+code).attr({"data-durationtype": durationtype, "data-duration": duration});
		load_chart("chg_chart", code, durationtype, duration);
	});
	// 차트 로드
	// 리스트 로드
		$(".list").not(".not").each(function(){
			url = $(this).data("listtype") ? "/ajax/load_list"+$(this).data("listtype")+".php" : "/ajax/load_list.php";
			type = $(this).data("type");
			limit = $(this).data("limit");
			$(this).attr("id", "list_"+type);
			$.ajax({
				url: url,
				type: "POST",
				async: false,
				data: {"type": type, "limit": limit},
				success: function(data){
					$("#list_"+type).html(data);
					
				}
			});	
		});

	// 리스트 로드
});
// 자산 로드
var asset_order = ["stock", "balance", "sum"], i;
function load_asset(id){
	var tmp;
	$.ajax({
		url: "/ajax/load_asset.php",
		type: "POST",
		data: {"id": id},
		sync: false,
		success: function(data){
			tmp = data.split("/");


			for(i in tmp){
				if(tmp[i] == ""){
					tmp[i] = "1만 이하";
				}
			}
			$("#asset_stock").html(tmp[0]+" ");

			$("#asset_balance").html(tmp[1]+" ");

			$("#asset_sum").html(tmp[2]+" ");

			$("#asset_ratio").html(tmp[3]+" ");
			if(tmp[3].replace("%", "") > 0){
				$("#asset_ratio").addClass("up");
			}else{
				$("#asset_ratio").addClass("down");
			}
		}
	});
}
// 자산 로드
function load_chart(type, code_new, durationtype, duration){
	$(".chart").each(function(){
		code = $(this).data("code");
		if(type=="all" && code!=code_new){
			duration = $(this).data("duration");
			durationtype = $(this).data("durationtype");
		}
		height = parseInt($(this).data("height")) > 0 ? $(this).data("height") : 300; 
		title = $(this).data("title");
		total = $(this).data("total");
		size = $(this).parent().width();
		
		$(this).attr("id", "area_"+code);
		$(this).animate({"height": height+54}, 500);	
		$(this).css("opacity", 0);
		
		$.ajax({
			url: "/ajax/load_chart.php",
			type: "POST",
			sync: false,
			data: {"code": code, "size": size, "duration": duration, "durationtype": durationtype, "title": title, "total": total, "height": height},
			async: false,
			success: function(data){
				$("#area_"+code).html(data);
			}
		});
	});
	$(".chart").animate({"opacity": 1});
}

// 종목 토론실
// WRITE
function talk_write(code){
	console.log(code);
	$(".layout-view").hide();
	$(".layout-list").hide();
	$(".layout-write").show();

	resetWriteFrm();
}
// WRITE - AJAX SEND
function write(){
	if(writeFrmChk()){
		var code = $(".write_code").val();
		var subject = $(".write_subject input").val();
		var opinion = $(':radio[name="opinion"]:checked').val();
		var content = $("#ir1").val();
		$.post("/ajax/write.php", {"code": code, "subject": subject, "opinion": opinion, "content": content}, function(data){
			if(data[0] == "E"){
				var errorLog = "";
				switch(data.substr(1)){
					case "01": // 비로그인 사용자
						errorLog = "로그인해주세요";
						break;
					case "02": // 빈값 전송
						errorLog = "올바르지 않은 접근입니다";
						break;
					case "03": // 공백 전송
						errorLog = "올바르지 않은 접근입니다";
						break;
					case "04": // 연속 글 작성
						errorLog = "연속으로 글을 작성하실 수 없습니다";
						break;
					default:
						errorLog = "알수 없는 오류입니다 관리자에게 문의하세요";
						break;
				}
				alert(errorLog);
				return false;
			}else{
				resetWriteFrm();
				read(data);
			}
		});
	}
}
function writeFrmChk(){
	return true;
}
function resetWriteFrm(){
	$(".write_subject input").val("");
	$(".write_opinion input").removeAttr("checked");
	oEditors.getById["ir1"].exec("SET_CONTENTS", [""]);	// RE-SET CONTENT
	oEditors.getById["ir1"].exec("CHANGE_EDITING_MODE", ["WYSIWYG"]); 
}

function load_board(code, type, sel){
	$(sel).html("");
	$(".layout-write").hide();
	$(".layout-view").hide();
	$(sel).parent().parent().show();
	$(sel).parent().parent().find(".error").remove();

	// LIST
	if(type=="list"){
		$.get("/ajax/load_board.php", {"code": code}, function(data){
			data = JSON.parse(data);
			console.log(data);

			if(typeof data.error != "undefined"){
				console.log("List Load ERR : "+data.error);
				var errorLog = "";
				switch(data.error){
					case "01": // 올바르지 않은 인덱스 번호
						errorLog = "올바르지 않은 호출입니다";
						break;
					case "02": // 게시물 목록 이 존재하지 않음
						errorLog = "게시물이 없습니다";
						break;
				}
				$(sel).parent().parent().append(
					$("<h1/>").addClass("error").text(errorLog)
				);

				return false;
			}
			for(i in data){
				var post = data[i];
				var idx;
				var html = "<tr>";
				for(j in post){
					if(j=="idx"){
						idx = post[j];
						continue;
					}

					html += "<td class='";
					html += j;
					if(j=="recommend" && post[j] > 0){
						html += " exists"
					}

					if(j=="opinion"){
						var opinion = getOpinionKor(post[j]);
						html += " opinion_colorset_"+post[j]+"";
						post[j] = opinion;
					}
					html += "'";

					html += ">";

					if(j=="subject")
						html += "<a href='#talk' onclick='read("+idx+");'>";

					html += post[j];

					if(j=="subject")
						html += "</a>";

					html += "</td>";
				}
				html += "</tr>"
//				console.log(html);
				$(sel).append(html);
			}
			location.hash = "talk";
		});
	}
}
// READ
function read(idx){
	resetWriteFrm();
	$(".layout-write").hide();
	$(".talk .layout-list").hide();
	$(".talk .layout-view").show().find(".read").html("");
	$.get("/ajax/load_board.php", {"type": "read", "idx": idx, "code": "000000"}, function(data){
		data = JSON.parse(data);

		if(typeof data.error != "undefined"){
			console.log("List Load ERR : "+data.error);
			var errorLog = "";
			switch(data.error){
				case "01": // 코드 전송 오류
					errorLog = "올바르지 않은 접근입니다";
					break;
				case "02": // 올바르지 않은 게시물 번호
					errorLog = "올바르지 않은 접근입니다";
					break;
				case "03": // 삭제된 게시물
					errorLog = "삭제된 게시물입니다";
					break;
			}
			$(".talk .layout-view").append(
				$("<h1/>").addClass("error").text(errorLog)
			);
			return false;
		}

		for(i in data[0]){
			// $("<div/>").addClass("read_"+i).html(data[0][i]);
			if(i=="opinion"){
				data[0][i] = $("<span/>").addClass("opinion_colorset_"+data[0][i]).text(getOpinionKor(data[0][i]));
			}
			$(".read_"+i).html(data[0][i]);
			console.log(data[0][i]);
		}
		location.hash = "talk"+idx;

	});
}
//VOTE-UP
function recommend(idx){
	$.get("/ajax/recommend.php", {"idx": idx}, function(data){
		if(data[0] == "E"){
			var errorLog = "";
			switch(data.substr(1)){
				case "01": // 비로그인 사용자
					errorLog = "로그인해주세요";
					break;
				case "02": // 인덱스 전송 오류
					errorLog = "올바르지 않은 접근입니다";
					break;
				case "03": // 존재하지 않는 게시물
					errorLog = "존재하지 않는 게시물입니다";
					break;
				case "98": // 이미 추천한 게시물
					errorLog = "이미 추천하셨습니다";
					break;
				default:
					errorLog = "알수 없는 오류입니다";
					break;
			}
			alert(errorLog);
			return false;
		}else{
			$(".read_recommend ").html(data);
		}
	});
}
// Functions
function frmChk(frm){
	var arg;
	var argLen = arguments.length - 1;
	var i;
	
	for(i=argLen;i>=1;i--){
		arg = arguments[i];
		if(frm[arg].value.length == 0){
//			frm[arg].style.backgroundColor = "#fee";
		}else{
//			frm[arg].style.backgroundColor = "#fff";
		}
	}

	for(i=1;i<=argLen;i++){
		arg = arguments[i];
		if(frm[arg].value.length == 0){
			alert(frm[arg].title + "을(를) 입력해주세요");
			return false;
		}
	}
	return true;
}
function getOpinionKor(idx){
	var opinion = "오류";
	switch(parseInt(idx)){
		case 1:
			opinion = "강력매도";
			break;
		case 2:
			opinion = "매도";
			break;
		case 3:
			opinion = "중립"
			break;
		case 4:
			opinion = "매수";
			break;
		case 5:
			opinion = "걍력매수";
			break;
	}
	return opinion;
}
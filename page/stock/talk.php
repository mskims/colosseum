
<!-- LIST -->
<div class="layout-list">

<button type="button" class="talk_write" onclick="talk_write('<?=$code?>')">글쓰기</button>
<table>
	<colgroup>
		<col style="width: 50%; " />
		<col style="width: 15%; " />
		<col style="width: 10%; " />
		<col style="width: 10%; " />
		<col style="width: 15%; " />
	</colgroup>
	<thead>
		<tr>
			<th>제목</th>
			<th>작성자</th>
			<th>투자의견</th>
			<th>추천</th>
			<th>날짜</th>
		</tr>
	</thead>
	<tbody>
	
	</tbody>
</table>
</div>




<!-- VIEW -->
<div class="layout-view">
	<div class="hidden_datas">
		<div class="read read_idx"></div>
		<div class="read read_hit"></div>
	</div>
	<div class="header">
		<div class="read read_subject"></div>
		<div class="read read_nick"></div>
	</div>
	<div class="read_content-wrap">
		<div class="read read_regdate"></div>
		<div class="read read_content"></div>
		<div class="read_recommend-wrap">
			<div class="read_recommend_button" onclick="recommend($('.read_idx').html());">
				추천 <a class="read read_recommend up"></a>
			</div>
		</div>
		<div class="view-info-wrap">
			투자의견 <div class="read read_opinion"></div>
			조회 <div class="read read_hit"></div>		
		</div>
		<div class="view-tools">
			<button type="button"></button>
		</div>
	</div>
</div>



<style type="text/css">
.write_subject { width: 100%; line-height: 30px; }
	.tr .left { width: 15%; float: left; text-align:center; }
	.tr .right { width: 85%; float: left; }
	.tr input { width: 100%; padding: 5px 0 5px 10px; }

	.write_opinion { width: 100%; line-height: 30px; }
		.write_opinion .right { }
		.write_opinion input { width: auto; vertical-align: middle; }
		.write_opinion label { margin-right: 10px; vertical-align:middle; margin-left: 5px;}

	.write_content { margin-top: 3px; }

	.write_tools { text-align:right; }
		.write_tools button { border-color: #cecece; box-shadow: none; }

		.write_tools button:hover { background: #fff; color: #333; }

		.write_tools button.submit:hover { background: #5cb85c; border-color: #4cae4c; color: #fff; }
		.write_tools button.cancel:hover { background: #d2322d; border-color: #ac2925; color: #fff; }
</style>
<!-- WRITE -->
<div class="layout-write dn">
	<form action="#" method="post" id="write_form" onsubmit="return submitCustom(this);">
		<input type="hidden" name="action" value="write" />
		<input type="hidden" name="code" value="<?=$s1["idx"]?>" class="write_code"/>
		<div class="tr write_subject clear">
			<div class="left">제목</div>
			<div class="right">
				<input type="text" name="subject" id="write_subject" title="제목"/>
			</div>
		</div>
		<div class="tr write_opinion clear">
			<div class="left">투자의견</div>
			<div class="right">
				<input type="radio" name="opinion" id="opinion_1" value="1" /><label class="opinion_colorset_1" for="opinion_1">강력매도</label>
				<input type="radio" name="opinion" id="opinion_2" value="2" /><label class="opinion_colorset_2" for="opinion_2">매도</label>
				<input type="radio" name="opinion" id="opinion_3" value="3" checked="checked" /><label class="opinion_colorset_3" for="opinion_3">중립</label>
				<input type="radio" name="opinion" id="opinion_4" value="4" /><label class="opinion_colorset_4" for="opinion_4">매수</label>
				<input type="radio" name="opinion" id="opinion_5" value="5" /><label class="opinion_colorset_5" for="opinion_5">강력매수</label>
			</div>
		</div>
		<div class="tr write-content">
			<script type="text/javascript" src="/common/se2/js/HuskyEZCreator.js" charset="utf-8"></script>
			<textarea name="ir1" id="ir1" rows="10" cols="100"></textarea>
			<script type="text/javascript">
			var oEditors = [];
			nhn.husky.EZCreator.createInIFrame({
				oAppRef: oEditors,
				elPlaceHolder: "ir1",
				sSkinURI: "/common/se2/SmartEditor2Skin.html",
				fCreator: "createSEditor2",
				htParams : { 
                  fOnBeforeUnload : function(){ 
                     //alert(&quot;onbeforeunload call&quot;);
                }}
			});
			</script>
		</div>
		<div class="tr write_tools">
			<button type="button" class="cancel" onclick="if(confirm('글작성을 취소하시겠습니까?')){load_board('<?=$s1["idx"]?>', 'list', '.talk .layout-list tbody');}">취소</button>
			<button type="submit" class="submit">작성</button>
		</div>
	</form>
	<script>
	function submitContents(elClickedObj) {			
		oEditors.getById["ir1"].exec("UPDATE_CONTENTS_FIELD", []);
		write();
	}
	function submitCustom(frmOjb){
		if(frmChk(document.getElementById('write_form'), 'subject')){
			submitContents(frmOjb);
			return false;
		}else{
			return false;
		}
	}
	</script>
</div>
<script type="text/javascript">
$(function(){
	load_board(<?=$boardIdx?>, "<?=$boardAction?>", ".talk .layout-list<?=$boardAction=='list' ? ' tbody' : ''?>");
});
</script>
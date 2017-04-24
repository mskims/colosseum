<table>
<colgroup>
	<col style="width: 30%; " />
	<col style="width: 70%; " />
</colgroup>
<?php
	$n = 0;
	$res_news = sql("select * from news where idx_s={$s1['idx']} order by idx desc");
	while($data_news = $res_news -> fetch()){ $n++; ?>
	<tr>
		<?php if(!empty($data_news["img"])){ ?>
		<td><img src="/static/img/news/<?=$data_news["img"]?>" alt="" /></td>
		<?php } ?>
		<td<?=empty($data_news["img"]) ? " colspan='2'" : ""?>>
			<div class="news_title"><?=$data_news["subject"]?></div>
			<div class="news_preview"><?=$data_news["content"]?></div>
		</td>
	</tr>
	<?php }
	if($n == 0){ ?>
	<tr>
		<td colspan="2">
			<h1 class="al_c">등록된 뉴스가 없습니다</h1>
		</td>
	</tr>
	<?php } ?>
</table>
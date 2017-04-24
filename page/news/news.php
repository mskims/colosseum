<div class="box_wrap c8">
	<div class="box">
		<div class="btitle">속보</div>
		<div class="news">
			<table>
			<colgroup>
				<col style="width: 30%; " />
				<col style="width: 70%; " />
			</colgroup>
			<?php
				$n = 0;
				$res_news = sql("select * from news order by idx desc");
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
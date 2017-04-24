<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/lib.php";

$code = $_POST["limit"];

$sql = "select sum(member_stock.count) as sum, member_stock.id, member.nick from member_stock join member on member.id = member_stock.id where member_stock.code = '{$code}' group by member_stock.id order by sum(member_stock.count) desc limit 3";
$result = $db->query($sql);

$first = true;
?>
<ul>
	<?php while($data=$result->fetch()){ ?>
	<li<?php if($first){ echo " style='font-weight: bold; '"; $first = false; }?>>
		<?php echo $data["nick"]; ?>
		<div class="f_right stock_count_counting"><?php echo number_format($data["sum"]); ?> 주</div>
	</li>
	<?php } ?>
</ul>
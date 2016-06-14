<div id="pagingLabel">
	Puslapiai:
</div>
<ul id="paging">
	<?php foreach ($pagingData as $key => $value) {
		$activeClass = "";
		if($value['isActive'] == 1) {
			$activeClass = " class='active'";
		}
		echo "<li{$activeClass}><a href='index.php?module={$module}&amp;page={$value['page']}' title=''>{$value['page']}</a></li>";
	} ?>
</ul>

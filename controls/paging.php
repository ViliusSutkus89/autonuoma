<div id="pagingLabel">
	Puslapiai:
</div>
<ul id="paging">
	<?php foreach ($paging->data as $key => $value) {
		$activeClass = "";
		if($value['isActive'] == 1) {
			$activeClass = " class='active'";
		}
		echo "<li{$activeClass}><a href='index.php?module={$module}&page={$value['page']}' title=''>{$value['page']}</a></li>";
	} ?>
</ul>
<div id="pagingLabel">
	Puslapiai:
</div>
<ul id="paging">
	<?php foreach ($pagingData as $key => $value) {
		$activeClass = "";
		if($value['isActive'] == 1) {
			$activeClass = " class='active'";
		}
    echo "<li{$activeClass}>",
      "<a href='", routing::getURL($module, '', "page=${value['page']}"), "'>",
      $value['page'], "</a></li>";
	} ?>
</ul>

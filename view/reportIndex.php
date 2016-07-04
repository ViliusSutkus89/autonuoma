<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li>Ataskaitos</li>
</ul>
<div id="actions"></div>

<div class="float-clear"></div>

<div class="page">
	<ul class="reportList">
<?php
foreach ($reports as $report_id => $report) {
  echo "<li>",
    "<p>",
    '<a href="',
      routing::getURL($module, 'view', "id={$report_id}"), '" ',
      "target='_blank' title='{$report['title']}'>", $report['title'],
    "</a></p>",
    "<p>", $report['description'], "</p>",
		"</li>\n";
}
?>
	</ul>
</div>
<?php require('footer.php');


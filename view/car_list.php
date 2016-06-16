<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li>Automobiliai</li>
</ul>
<div id="actions">
	<a href='index.php?module=<?php echo $module; ?>&action=new'>Naujas automobilis</a>
</div>
<div class="float-clear"></div>

<?php if(!empty($remove_error)) { ?>
	<div class="errorBox">
		Automobilis nebuvo pašalinta, nes yra įtrauktas į sutartį (-is).
	</div>
<?php } ?>

<table>
	<tr>
		<th>ID</th>
		<th>Valstybinis nr.</th>
		<th>Modelis</th>
		<th>Būsena</th>
		<th></th>
	</tr>
	<?php
		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['id']}</td>"
					. "<td>{$val['valstybinis_nr']}</td>"
					. "<td>{$val['marke']} {$val['modelis']}</td>"
					. "<td>{$val['busena']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['id']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='index.php?module={$module}&amp;id={$val['id']}' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>\n";
		}
	?>
</table>

<?php
require('paging.php');
require('footer.php');


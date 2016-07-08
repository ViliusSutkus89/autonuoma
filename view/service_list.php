<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li>Papildomos paslaugos</li>
</ul>
<div id="actions">
  <a href="<?php echo routing::getURL('report', 'view', 'id=2'); ?>" target="_blank">Paslaugų ataskaita</a>
	<a href='<?php echo routing::getURL($module, 'create'); ?>'>Nauja paslauga</a>
</div>
<div class="float-clear"></div>

<?php if(!empty($delete_error)) { ?>
	<div class="errorBox">
		Paslauga nebuvo pašalinta.
	</div>
<?php } ?>

<?php if(!empty($id_error)) { ?>
  <div class="errorBox">
    Paslauga nerasta!
  </div>
<?php } ?>

<table>
	<tr>
		<th>ID</th>
		<th>Pavadinimas</th>
		<th></th>
	</tr>
	<?php
		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['id']}</td>"
					. "<td>{$val['pavadinimas']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['id']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='" . routing::getURL($module, 'edit', 'id=' . $val['id']), "' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>\n";
		}
	?>
</table>

<?php
// įtraukiame puslapių šabloną
require('paging.php');
require('footer.php');


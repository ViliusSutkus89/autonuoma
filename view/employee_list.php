<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li>Darbuotojai</li>
</ul>
<div id="actions">
	<a href='<?php echo routing::getURL($module, 'create'); ?>'>Naujas darbuotojas</a>
</div>
<div class="float-clear"></div>

<?php if(!empty($delete_error)) { ?>
	<div class="errorBox">
    Darbuotojas nebuvo pašalintas, nes turi užsakymą (-ų).
	</div>
<?php } ?>

<?php if(!empty($id_error)) { ?>
  <div class="errorBox">
    Darbuotojas nerastas!
  </div>
<?php } ?>

<table>
	<tr>
		<th>Tabelio nr.</th>
		<th>Vardas</th>
		<th>Pavardė</th>
		<th></th>
	</tr>
	<?php


		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['tabelio_nr']}</td>"
					. "<td>{$val['vardas']}</td>"
					. "<td>{$val['pavarde']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['tabelio_nr']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='" . routing::getURL($module, 'edit', 'id=' . $val['tabelio_nr']), "' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>\n";
		}
	?>
</table>

<?php
// įtraukiame puslapių šabloną
require('paging.php');
require('footer.php');


<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li>Klientai</li>
</ul>
<div id="actions">
	<a href='<?php echo routing::getURL($module, 'create'); ?>'>Naujas klientas</a>
</div>
<div class="float-clear"></div>

<?php if(!empty($delete_error)) { ?>
	<div class="errorBox">
		Klientas nebuvo pašalintas, nes turi užsakymą (-ų).
	</div>
<?php } ?>

<?php if(!empty($id_error)) { ?>
  <div class="errorBox">
    Klientas nerastas!
  </div>
<?php } ?>

<table>
	<tr>
		<th>ID</th>
		<th>Vardas</th>
		<th>Pavardė</th>
		<th>Gimimo data</th>
		<th></th>
	</tr>
	<?php

		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['asmens_kodas']}</td>"
					. "<td>{$val['vardas']}</td>"
					. "<td>{$val['pavarde']}</td>"
					. "<td>{$val['gimimo_data']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['asmens_kodas']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='" . routing::getURL($module, 'edit', 'id=' . $val['asmens_kodas']), "' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>\n";
		}
	?>
</table>

<?php
// įtraukiame puslapių šabloną
require('paging.php');
require('footer.php');


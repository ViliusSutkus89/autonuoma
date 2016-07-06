<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li>Automobilių modeliai</li>
</ul>
<div id="actions">
  <a href='<?php echo routing::getURL($module, 'create'); ?>'>Naujas modelis</a>
</div>
<div class="float-clear"></div>

<?php if(!empty($delete_error)) { ?>
	<div class="errorBox">
		Modelis nebuvo pašalintas. Pirmiausia pašalinkite to modelio automobilius.
	</div>
<?php } ?>

<?php if(!empty($id_error)) { ?>
  <div class="errorBox">
    Modelis nerastas!
  </div>
<?php } ?>

<table>
	<tr>
		<th>ID</th>
		<th>Markė</th>
		<th>Modelis</th>
		<th></th>
	</tr>
	<?php
		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['id']}</td>"
					. "<td>{$val['marke']}</td>"
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
require('paging.php');
require('footer.php');


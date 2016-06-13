<?php
	// sukuriame modelių klasės objektą
	include 'libraries/models.class.php';
	$modelsObj = new models();
	
	// sukuriame puslapiavimo klasės objektą
	include 'utils/paging.class.php';
	$paging = new paging(NUMBER_OF_ROWS_IN_PAGE);
	
	if(!empty($removeId)) {
		// patikriname, ar šalinamas modelis nenaudojamas, t.y. nepriskirtas jokiam automobiliui
		$count = $modelsObj->getCarCountOfModel($removeId);
		
		$removeErrorParameter = '';
		if($count == 0) {
			// pašaliname modelį
			$modelsObj->deleteModel($removeId);
		} else {
			// nepašalinome, nes modelis priskirtas bent vienam automobiliui, rodome klaidos pranešimą
			$removeErrorParameter = '&remove_error=1';
		}
		
		// nukreipiame į modelių puslapį
		header("Location: index.php?module={$module}{$removeErrorParameter}");
		die();
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li>Automobilių modeliai</li>
</ul>
<div id="actions">
	<a href='index.php?module=<?php echo $module; ?>&action=new'>Naujas modelis</a>
</div>
<div class="float-clear"></div>

<?php if(isset($_GET['remove_error'])) { ?>
	<div class="errorBox">
		Modelis nebuvo pašalintas. Pirmiausia pašalinkite to modelio automobilius.
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
		// suskaičiuojame bendrą įrašų kiekį
		$elementCount = $modelsObj->getModelListCount();

		// suformuojame sąrašo puslapius
		$paging->process($elementCount, $pageId);

		// išrenkame nurodyto puslapio modelius
		$data = $modelsObj->getModelList($paging->size, $paging->first);

		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['id']}</td>"
					. "<td>{$val['marke']}</td>"
					. "<td>{$val['pavadinimas']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['id']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='index.php?module={$module}&id={$val['id']}' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>";
		}
	?>
</table>

<?php
	// įtraukiame puslapių šabloną
	include 'controls/paging.php';
?>
<?php

	// sukuriame markių klasės objektą
	include 'libraries/brands.class.php';
	$brandsObj = new brands();
	
	// sukuriame puslapiavimo klasės objektą
	include 'utils/paging.class.php';
	$paging = new paging(NUMBER_OF_ROWS_IN_PAGE);
	
	if(!empty($removeId)) {
		// patikriname, ar šalinama markė nepriskirta modeliui
		$count = $brandsObj->getModelCountOfBrand($removeId);
		
		$removeErrorParameter = '';
		if($count == 0) {
			// šaliname markę
			$brandsObj->deleteBrand($removeId);
		} else {
			// nepašalinome, nes markė priskirta modeliui, rodome klaidos pranešimą
			$removeErrorParameter = '$remove_error=1';
		}

		// nukreipiame į markių puslapį
		header("Location: index.php?module={$module}{$removeErrorParameter}");
		die();
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li>Automobilių markės</li>
</ul>
<div id="actions">
	<a href='index.php?module=<?php echo $module; ?>&action=new'>Nauja markė</a>
</div>
<div class="float-clear"></div>

<?php if(isset($_GET['remove_error'])) { ?>
	<div class="errorBox">
		Markė nebuvo pašalinta. Pirmiausia pašalinkite markės modelius.
	</div>
<?php } ?>

<table>
	<tr>
		<th>ID</th>
		<th>Pavadinimas</th>
		<th></th>
	</tr>
	<?php
		// suskaičiuojame bendrą įrašų kiekį
		$elementCount = $brandsObj->getBrandListCount();

		// suformuojame sąrašo puslapius
		$paging->process($elementCount, $pageId);

		// išrenkame nurodyto puslapio markes
		$data = $brandsObj->getBrandList($paging->size, $paging->first);

		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['id']}</td>"
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
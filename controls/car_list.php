<?php

	// sukuriame automobilių klasės objektą
	include 'libraries/cars.class.php';
	$carsObj = new cars();
	
	// sukuriame puslapiavimo klasės objektą
	include 'utils/paging.class.php';
	$paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

	if(!empty($removeId)) {
		// patikriname, ar automobilis neįtrauktas į sutartis
		$count = $carsObj->getContractCountOfCar($removeId);
	
		$removeErrorParameter = '';
		if($count == 0) {
			// šaliname automobilį
			$carsObj->deleteCar($removeId);
		} else {
			// nepašalinome, nes automobilis įtrauktas bent į vieną sutartį, rodome klaidos pranešimą
			$removeErrorParameter = '&remove_error=1';
		}

		// nukreipiame į automobilių puslapį
		header("Location: index.php?module={$module}{$removeErrorParameter}");
		die();
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li>Automobiliai</li>
</ul>
<div id="actions">
	<a href='index.php?module=<?php echo $module; ?>&action=new'>Naujas automobilis</a>
</div>
<div class="float-clear"></div>

<?php if(isset($_GET['remove_error'])) { ?>
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
		// suskaičiuojame bendrą įrašų kiekį
		$elementCount = $carsObj->getCarListCount();

		// suformuojame sąrašo puslapius
		$paging->process($elementCount, $pageId);

		// išrenkame nurodyto puslapio automobilius
		$data = $carsObj->getCarList($paging->size, $paging->first);

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
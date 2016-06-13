<?php
	
	// sukuriame darbuotojų klasės objektą
	include 'libraries/employees.class.php';
	$employeesObj = new employees();

	// sukuriame puslapiavimo klasės objektą
	include 'utils/paging.class.php';
	$paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

	if(!empty($removeId)) {
		// patikriname, ar darbuotojas neturi sudarytų sutarčių
		$count = $employeesObj->getContractCountOfEmployee($removeId);
		
		$removeErrorParameter = '';
		if($count == 0) {
			// šaliname darbuotoją
			$employeesObj->deleteEmployee($removeId);
		} else {
			// nepašalinome, nes darbuotojas sudaręs bent vieną sutartį, rodome klaidos pranešimą
			$removeErrorParameter = '&remove_error=1';
		}

		// nukreipiame į darbuotojų puslapį
		header("Location: index.php?module={$module}{$removeErrorParameter}");
		die();
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li>Darbuotojai</li>
</ul>
<div id="actions">
	<a href='index.php?module=<?php echo $module; ?>&action=new'>Naujas darbuotojas</a>
</div>
<div class="float-clear"></div>

<?php if(isset($_GET['remove_error'])) { ?>
	<div class="errorBox">
		Klientas nebuvo pašalintas, nes turi užsakymą (-ų).
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
		// suskaičiuojame bendrą įrašų kiekį
		$elementCount = $employeesObj->getEmplyeesListCount();

		// suformuojame sąrašo puslapius
		$paging->process($elementCount, $pageId);

		// išrenkame nurodyto puslapio darbuotojus
		$data = $employeesObj->getEmplyeesList($paging->size, $paging->first);

		// suformuojame lentelę
		foreach($data as $key => $val) {
			echo
				"<tr>"
					. "<td>{$val['tabelio_nr']}</td>"
					. "<td>{$val['vardas']}</td>"
					. "<td>{$val['pavarde']}</td>"
					. "<td>"
						. "<a href='#' onclick='showConfirmDialog(\"{$module}\", \"{$val['tabelio_nr']}\"); return false;' title=''>šalinti</a>&nbsp;"
						. "<a href='index.php?module={$module}&id={$val['tabelio_nr']}' title=''>redaguoti</a>"
					. "</td>"
				. "</tr>";
		}
	?>
</table>

<?php
	// įtraukiame puslapių šabloną
	include 'controls/paging.php';
?>
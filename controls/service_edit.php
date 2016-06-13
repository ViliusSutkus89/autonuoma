<?php
	
	include 'libraries/contracts.class.php';
	$contractsObj = new contracts();

	include 'libraries/services.class.php';
	$servicesObj = new services();
	
	$formErrors = null;
	$fields = array();
	
	// nustatome privalomus laukus
	$required = array('pavadinimas', 'kainos', 'datos');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'pavadinimas' => 40,
		'aprasymas' => 300
	);
	
	// paspaustas išsaugojimo mygtukas
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array (
			'pavadinimas' => 'anything',
			'aprasymas' => 'anything',
			'kainos' => 'price',
			'datos' => 'date');
		
		// sukuriame validatoriaus objektą
		include 'utils/validator.class.php';
		$validator = new validator($validations, $required, $maxLengths);
		
		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$servicesObj->updateService($data);
				
				// pašaliname paslaugos kainas, kurios nėra naudojamos sutartyse
				$deleteQueryClause = "";
				foreach($data['kainos'] as $key=>$val) {
					if($data['neaktyvus'][$key] == 1) {
						$deleteQueryClause .= " AND NOT `galioja_nuo`='" . $data['datos'][$key] . "'";
					}
				}
				$servicesObj->deleteServicePrices($data['id'], $deleteQueryClause);
				
				// atnaujiname paslaugos kainas, kurios nėra naudojamos sutartyse
				$servicesObj->insertServicePrices($data);
			} else {
				// randame didžiausią paslaugos numeri duomenų bazėje
				$latestId = $servicesObj->getMaxIdOfService();
				
				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$servicesObj->insertService($data);

				// įrašome paslaugų kainas
				$servicesObj->insertServicePrices($data);
			}
			
			// nukreipiame į modelių puslapį
			header("Location: index.php?module={$module}");
			die();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// gauname įvestus laukus
			$fields = $_POST;
			if(isset($_POST['kainos']) && sizeof($_POST['kainos']) > 0) {
				$i = 0;
				foreach($_POST['kainos'] as $key => $val) {
					$fields['paslaugos_kainos'][$i]['kaina'] = $val;
					$fields['paslaugos_kainos'][$i]['galioja_nuo'] = $_POST['datos'][$key];
					$fields['paslaugos_kainos'][$i]['neaktyvus'] = $_POST['neaktyvus'][$key];
					$i++;
				}
			}
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			$fields = $servicesObj->getService($id);
			$tmp = $servicesObj->getServicePrices($id);
			if(sizeof($tmp) > 0) {
				foreach($tmp as $key => $val) {
					// jeigu paslaugos kaina yra naudojama, jos koreguoti neleidziame ir įvedimo laukelį padarome neaktyvų
					$priceCount = $contractsObj->getPricesCountOfOrderedServices($id, $val['galioja_nuo']);
					if($priceCount > 0) {
						$val['neaktyvus'] = 1;
					}
					$fields['paslaugos_kainos'][] = $val;
				}
			}
		}
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Papildomos paslaugos</a></li>
	<li><?php if(!empty($id)) echo "Paslaugos redagavimas"; else echo "Nauja paslauga"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
	<?php if($formErrors != null) { ?>
		<div class="errorBox">
			Neįvesti arba neteisingai įvesti šie laukai:
			<?php 
				echo $formErrors;
			?>
		</div>
	<?php } ?>
	<form action="" method="post">
		<fieldset>
			<legend>Papildomos paslaugos informacija</legend>
			<p>
				<label class="field" for="pavadinimas">Pavadinimas<?php echo in_array('pavadinimas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="pavadinimas" name="pavadinimas" class="textbox-150" value="<?php echo isset($fields['pavadinimas']) ? $fields['pavadinimas'] : ''; ?>">
				<?php if(key_exists('pavadinimas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['pavadinimas']} simb.)</span>"; ?>
			</p>
			<p>
				<label class="field" for="aprasymas">Aprašymas<?php echo in_array('aprasymas', $required) ? '<span> *</span>' : ''; ?></label>
				<textarea id="aprasymas" name="aprasymas" class=""><?php echo isset($fields['aprasymas']) ? $fields['aprasymas'] : ''; ?></textarea>
				<?php if(key_exists('aprasymas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['aprasymas']} simb.)</span>"; ?>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>Paslaugos kainos</legend>
			<div class="childRowContainer">
				<div class="labelLeft<?php if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) echo ' hidden'; ?>">Kaina</div>
				<div class="labelRight<?php if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) echo ' hidden'; ?>">Galioja nuo</div>
				<div class="float-clear"></div>
				<?php
					if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) {
				?>
					
					<div class="childRow hidden">
						<input type="text" name="kainos[]" value="" class="textbox-70" disabled="disabled" />
						<input type="text" name="datos[]" value="" class="textbox-70" disabled="disabled" />
						<input type="hidden" class="isDisabledForEditing" name="neaktyvus[]" value="0" />
						<a href="#" title="" class="removeChild">šalinti</a>
					</div>
					<div class="float-clear"></div>
					
				<?php
					} else {
						foreach($fields['paslaugos_kainos'] as $key => $val) {
				?>
							<div class="childRow">
								<input type="text" name="kainos[]" value="<?php echo $val['kaina']; ?>" class="textbox-70<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo ' disabledInput'; ?>" />
								<input type="text" name="datos[]" value="<?php echo $val['galioja_nuo']; ?>" class="textbox-70<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo ' disabledInput'; ?>" />
								<input type="hidden" class="isDisabledForEditing" name="neaktyvus[]" value="<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo "1"; else echo "0"; ?>" />
								<a href="#" title="" class="removeChild<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo " hidden"; ?>">šalinti</a>
							</div>
							<div class="float-clear"></div>
				<?php 
						}
					}
				?>
			</div>
			<p id="newItemButtonContainer">
				<a href="#" title="" class="addChild">Pridėti</a>
			</p>
		</fieldset>
		
		<p class="required-note">* pažymėtus laukus užpildyti privaloma</p>
		<p>
			<input type="submit" class="submit" name="submit" value="Išsaugoti">
		</p>
		<?php if(isset($fields['id'])) { ?>
			<input type="hidden" name="id" value="<?php echo $fields['id']; ?>" />
		<?php } ?>
	</form>
</div>
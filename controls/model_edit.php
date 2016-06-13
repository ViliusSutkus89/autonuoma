<?php

	include 'libraries/brands.class.php';
	$brandsObj = new brands();

	include 'libraries/models.class.php';
	$modelsObj = new models();
	
	$formErrors = null;
	$fields = array();
	
	// nustatome privalomus laukus
	$required = array('pavadinimas', 'fk_marke');

	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'pavadinimas' => 20
	);
	
	// paspaustas išsaugojimo mygtukas
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array (
			'pavadinimas' => 'anything',
			'fk_marke' => 'positivenumber');
		
		// sukuriame validatoriaus objektą
		include 'utils/validator.class.php';
		$validator = new validator($validations, $required, $maxLengths);
		
		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$modelsObj->updateModel($data);
			} else {
				// randame didžiausią modelio id duomenų bazėje
				$latestId = $modelsObj->getMaxIdOfModel();

				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$modelsObj->insertModel($data);
			}
			
			// nukreipiame į modelių puslapį
			header("Location: index.php?module={$module}");
			die();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// gauname įvestus laukus
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			$fields = $modelsObj->getModel($id);
		}
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Automobilių modeliai</a></li>
	<li><?php if(!empty($id)) echo "Modelio redagavimas"; else echo "Naujas modelis"; ?></li>
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
			<legend>Modelio informacija</legend>
			<p>
				<label class="field" for="brand">Markė<?php echo in_array('fk_marke', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="brand" name="fk_marke">
					<option value="-1">Pasirinkite markę</option>
					<?php
						// išrenkame visas markes
						$brands = $brandsObj->getBrandList();
						foreach($brands as $key => $val) {
							$selected = "";
							if(isset($fields['fk_marke']) && $fields['fk_marke'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['pavadinimas']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="name">Pavadinimas<?php echo in_array('pavadinimas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="name" name="pavadinimas" class="textbox-150" value="<?php echo isset($fields['pavadinimas']) ? $fields['pavadinimas'] : ''; ?>">
				<?php if(key_exists('pavadinimas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['pavadinimas']} simb.)</span>"; ?>
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
<?php
	
	include 'libraries/employees.class.php';
	$employeesObj = new employees();

	$formErrors = null;
	$fields = array();
	
	// nustatome privalomus formos laukus
	$required = array('tabelio_nr', 'vardas', 'pavarde');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'tabelio_nr' => 6,
		'vardas' => 20,
		'pavarde' => 20
	);
	
	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {
		include 'utils/validator.class.php';
		
		// nustatome laukų validatorių tipus
		$validations = array (
			'tabelio_nr' => 'alfanum',
			'vardas' => 'alfanum',
			'pavarde' => 'alfanum');
		
		// sukuriame laukų validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();

			if(isset($data['editing'])) {
				// redaguojame klientą
				$employeesObj->updateEmployee($data);
			} else {
				// įrašome naują klientą
				$employeesObj->insertEmployee($data);
			}

			// nukreipiame vartotoją į klientų puslapį
			header("Location: index.php?module={$module}");
			die();
		}
		else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			// išrenkame klientą
			$fields = $employeesObj->getEmployee($id);
			$fields['editing'] = 1;
		}
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Darbuotojai</a></li>
	<li><?php if(!empty($id)) echo "Darbuotojo redagavimas"; else echo "Naujas darbuotojas"; ?></li>
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
			<legend>Darbuotojo informacija</legend>
				<p>
					<label class="field" for="tabelio_nr">Tabelio numeris<?php echo in_array('tabelio_nr', $required) ? '<span> *</span>' : ''; ?></label>
					<?php if(!isset($fields['editing'])) { ?>
						<input type="text" id="tabelio_nr" name="tabelio_nr" class="textbox-150" value="<?php echo isset($fields['tabelio_nr']) ? $fields['tabelio_nr'] : ''; ?>" />
						<?php if(key_exists('tabelio_nr', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['tabelio_nr']} simb.)</span>"; ?>
					<?php } else { ?>
						<span class="input-value"><?php echo $fields['tabelio_nr']; ?></span>
						<input type="hidden" name="editing" value="1" />
						<input type="hidden" name="tabelio_nr" value="<?php echo $fields['tabelio_nr']; ?>" />
					<?php } ?>
				</p>
			<p>
				<label class="field" for="vardas">Vardas<?php echo in_array('vardas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="vardas" name="vardas" class="textbox-150" value="<?php echo isset($fields['vardas']) ? $fields['vardas'] : ''; ?>" />
				<?php if(key_exists('vardas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['vardas']} simb.)</span>"; ?>
			</p>
			<p>
				<label class="field" for="pavarde">Pavardė<?php echo in_array('pavarde', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="pavarde" name="pavarde" class="textbox-150" value="<?php echo isset($fields['pavarde']) ? $fields['pavarde'] : ''; ?>" />
				<?php if(key_exists('pavarde', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['pavarde']} simb.)</span>"; ?>
			</p>
		</fieldset>
		<p class="required-note">* pažymėtus laukus užpildyti privaloma</p>
		<p>
			<input type="submit" class="submit" name="submit" value="Išsaugoti">
		</p>
	</form>
</div>
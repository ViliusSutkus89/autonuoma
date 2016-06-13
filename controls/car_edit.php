<?php

	include 'libraries/cars.class.php';
	$carsObj = new cars();

	include 'libraries/brands.class.php';
	$brandsObj = new brands();

	include 'libraries/models.class.php';
	$modelsObj = new models();
	
	$formErrors = null;
	$fields = array();

	// nustatome privalomus laukus
	$required = array('modelis', 'valstybinis_nr', 'pagaminimo_data', 'pavaru_deze', 'degalu_tipas', 'kebulas', 'bagazo_dydis', 'busena', 'rida', 'vietu_skaicius', 'registravimo_data', 'verte');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'valstybinis_nr' => 6
	);
	
	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array (
			'modelis' => 'positivenumber',
			'valstybinis_nr' => 'alfanum',
			'pavaru_deze' => 'positivenumber',
			'degalu_tipas' => 'positivenumber',
			'kebulas' => 'positivenumber',
			'bagazo_dydis' => 'positivenumber',
			'busena' => 'positivenumber',
			'pagaminimo_data' => 'date',
			'rida' => 'positivenumber',
			'vietu_skaicius' => 'positivenumber',
			'registravimo_data' => 'date',
			'verte' => 'price'
			);
				
		// sukuriame laukų validatoriaus objektą
		include 'utils/validator.class.php';
		$validator = new validator($validations, $required, $maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			
			// sutvarkome checkbox reikšmes
			if(isset($data['radijas']) && $data['radijas'] == 'on') {
				$data['radijas'] = 1;
			} else {
				$data['radijas'] = 0;
			}

			if(isset($data['grotuvas']) && $data['grotuvas'] == 'on') {
				$data['grotuvas'] = 1;
			} else {
				$data['grotuvas'] = 0;
			}

			if(isset($data['kondicionierius']) && $data['kondicionierius'] == 'on') {
				$data['kondicionierius'] = 1;
			} else {
				$data['kondicionierius'] = 0;
			}
			
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$carsObj->updateCar($data);
			} else {
				// randame didžiausią automobilio id duomenų bazėje
				$latestId = $carsObj->getMaxIdOfCar();

				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$carsObj->insertCar($data);
			}
			
			// nukreipiame vartotoją į automobilių puslapį
			header("Location: index.php?module={$module}");
			die();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			// išrenkame automobilį
			$fields = $carsObj->getCar($id);
		}
	}
?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Automobiliai</a></li>
	<li><?php if(!empty($id)) echo "Automobilio redagavimas"; else echo "Naujas automobilis"; ?></li>
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
			<legend>Automobilio informacija</legend>
			<p>
				<label class="field" for="modelis">Modelis<?php echo in_array('modelis', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="modelis" name="modelis">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$brands = $brandsObj->getBrandList();
						foreach($brands as $key => $val) {
							echo "<optgroup label='{$val['pavadinimas']}'>";

							$models = $modelsObj->getModelListByBrand($val['id']);
							foreach($models as $key2 => $val2) {
								$selected = "";
								if(isset($fields['modelis']) && $fields['modelis'] == $val2['id']) {
									$selected = " selected='selected'";
								}
								echo "<option{$selected} value='{$val2['id']}'>{$val2['pavadinimas']}</option>";
							}
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="valstybinis_nr">Valstybinis numeris<?php echo in_array('valstybinis_nr', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="valstybinis_nr" name="valstybinis_nr" class="textbox-70" value="<?php echo isset($fields['valstybinis_nr']) ? $fields['valstybinis_nr'] : ''; ?>">
				<?php if(key_exists('valstybinis_nr', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['valstybinis_nr']} simb.)</span>"; ?>
			</p>
			<p>
				<label class="field" for="pagaminimo_data">Pagaminimo data<?php echo in_array('pagaminimo_data', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="pagaminimo_data" name="pagaminimo_data" class="textbox-70 date" value="<?php echo isset($fields['pagaminimo_data']) ? $fields['pagaminimo_data'] : ''; ?>"></p>
			<p>
				<label class="field" for="pavaru_deze">Pavarų dėžė<?php echo in_array('pavaru_deze', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="pavaru_deze" name="pavaru_deze">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$gearboxes = $carsObj->getGearboxList();
						foreach($gearboxes as $key => $val) {
							$selected = "";
							if(isset($fields['pavaru_deze']) && $fields['pavaru_deze'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['name']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="degalu_tipas">Degalų tipas<?php echo in_array('degalu_tipas', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="degalu_tipas" name="degalu_tipas">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$fueltypes = $carsObj->getFuelTypeList();
						foreach($fueltypes as $key => $val) {
							$selected = "";
							if(isset($fields['degalu_tipas']) && $fields['degalu_tipas'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['name']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="kebulas">Kėbulo tipas<?php echo in_array('kebulas', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="kebulas" name="kebulas">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$bodytypes = $carsObj->getBodyTypeList();
						foreach($bodytypes as $key => $val) {
							$selected = "";
							if(isset($fields['kebulas']) && $fields['kebulas'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['name']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="bagazo_dydis">Bagažo dydis<?php echo in_array('bagazo_dydis', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="bagazo_dydis" name="bagazo_dydis">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$lugage = $carsObj->getLugageTypeList();
						foreach($lugage as $key => $val) {
							$selected = "";
							if(isset($fields['bagazo_dydis']) && $fields['bagazo_dydis'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['name']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="busena">Būsena<?php echo in_array('busena', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="busena" name="busena">
					<option value="-1">---------------</option>
					<?php
						// išrenkame visas kategorijas sugeneruoti pasirinkimų lauką
						$car_states = $carsObj->getCarStateList();
						foreach($car_states as $key => $val) {
							$selected = "";
							if(isset($fields['busena']) && $fields['busena'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['name']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="rida">Rida<?php echo in_array('rida', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="rida" name="rida" class="textbox-70" value="<?php echo isset($fields['rida']) ? $fields['rida'] : ''; ?>"><span class="units">km.</span>
			</p>
			<p>
				<label class="field" for="radijas">Radijas</label>
				<input type="checkbox" id="radijas" name="radijas"<?php echo (isset($fields['radijas']) && ($fields['radijas'] == 1 || $fields['radijas'] == 'on'))  ? ' checked="checked"' : ''; ?>>
			</p>
			<p>
				<label class="field" for="grotuvas">Grotuvas</label>
				<input type="checkbox" id="grotuvas" name="grotuvas"<?php echo (isset($fields['grotuvas']) && ($fields['grotuvas'] == 1 || $fields['grotuvas'] == 'on'))  ? ' checked="checked"' : ''; ?>>
			</p>
			<p>
				<label class="field" for="kondicionierius">Kondicionierius</label>
				<input type="checkbox" id="kondicionierius" name="kondicionierius"<?php echo (isset($fields['kondicionierius']) && ($fields['kondicionierius'] == 1 || $fields['kondicionierius'] == 'on'))  ? ' checked="checked"' : ''; ?>>
			</p>
			<p>
				<label class="field" for="vietu_skaicius">Vietų skaičius<?php echo in_array('vietu_skaicius', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="vietu_skaicius" name="vietu_skaicius" class="textbox-30" value="<?php echo isset($fields['vietu_skaicius']) ? $fields['vietu_skaicius'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="registravimo_data">Registravimo data<?php echo in_array('registravimo_data', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="registravimo_data" name="registravimo_data" class="textbox-70 date" value="<?php echo isset($fields['registravimo_data']) ? $fields['registravimo_data'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="verte">Vertė<?php echo in_array('verte', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="verte" name="verte" class="textbox-70" value="<?php echo isset($fields['verte']) ? $fields['verte'] : ''; ?>"><span class="units">&euro;</span>
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
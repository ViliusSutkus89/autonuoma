<?php

	include 'libraries/contracts.class.php';
	$contractsObj = new contracts();
	
	$formErrors = null;
	$fields = array();
	$formSubmitted = false;
		
	$data = array();
	if(!empty($_POST['submit'])) {
		$formSubmitted = true;

		// nustatome laukų validatorių tipus
		$validations = array (
			'dataNuo' => 'date',
			'dataIki' => 'date');
		
		// sukuriame validatoriaus objektą
		include 'utils/validator.class.php';
		$validator = new validator($validations);
		

		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// gauname įvestus laukus
			$fields = $_POST;
		}
	}
	
if($formSubmitted == true && ($formErrors == null)) { ?>
	<div id="header">
		<ul id="reportInfo">
			<li class="title">Vėluojamų grąžinti automobilių ataskaita</li>
			<li>Sudarymo data: <span><?php echo date("Y-m-d"); ?></span></li>
			<li>Paslaugų užsakymo laikotarpis:
				<span>
					<?php
						if(!empty($data['dataNuo'])) {
							if(!empty($data['dataIki'])) {
								echo "nuo {$data['dataNuo']} iki {$data['dataIki']}";
							} else {
								echo "nuo {$data['dataNuo']}";
							}
						} else {
							if(!empty($data['dataIki'])) {
								echo "iki {$data['dataIki']}";
							} else {
								echo "nenurodyta";
							}
						}
					?>
				</span>
				<a href="report.php?id=3" title="Nauja ataskaita" class="newReport">nauja ataskaita</a>
			</li>
		</ul>
	</div>
<?php } ?>
<div id="content">
	<div id="contentMain">
		<?php
			if($formSubmitted == false || $formErrors != null) { ?>
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
							<legend>Įveskite ataskaitos kriterijus</legend>
							<p><label class="field" for="dataNuo">Sutartys sudarytos nuo</label><input type="text" id="dataNuo" name="dataNuo" class="textbox-100 date" value="<?php echo isset($fields['dataNuo']) ? $fields['dataNuo'] : ''; ?>" /></p>
							<p><label class="field" for="dataIki">Sutartys sudarytos iki</label><input type="text" id="dataIki" name="dataIki" class="textbox-100 date" value="<?php echo isset($fields['dataIki']) ? $fields['dataIki'] : ''; ?>" /></p>
						</fieldset>
						<p><input type="submit" class="submit" name="submit" value="Sudaryti ataskaitą"></p>
					</form>
				</div>
	<?php	} else {
					// išrenkame ataskaitos duomenis
					$delayedCarsData = $contractsObj->getDelayedCars($data['dataNuo'], $data['dataIki']);
					
					if(sizeof($delayedCarsData) > 0) { ?>
						<table class="reportTable">
							<tr>
								<th>Sutartis</th>
								<th>Klientas</th>
								<th>Planuota grąžinti</th>
								<th>Grąžinta</th>
							</tr>

							<tr>
								<td class="separator" colspan="5"></td>
							</tr>

							<?php
								// suformuojame lentelę
								foreach($delayedCarsData as $key => $val) {
									echo
										"<tr>"
											. "<td>#{$val['nr']}, {$val['sutarties_data']}</td>"
											. "<td>{$val['vardas']} {$val['pavarde']}</td>"
											. "<td>{$val['planuojama_grazinimo_data_laikas']}</td>"
											. "<td>{$val['grazinta']}</td>"
										. "</tr>";
								}
							?>
						</table>

			<?php   } else { ?>
						<div class="warningBox">
							Nurodytu laikotartpiu nerasta negrąžintų automobilių
						</div>
					<?php
					}
			}
			?>
	</div>
</div>
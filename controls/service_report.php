<?php

	include 'libraries/services.class.php';
	$servicesObj = new services();
	
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
			<li class="title">Užsakytų paslaugų ataskaita</li>
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
				<a href="report.php?id=2" title="Nauja ataskaita" class="newReport">nauja ataskaita</a>
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
							<p><label class="field" for="dataNuo">Paslaugos užsakytos nuo</label><input type="text" id="dataNuo" name="dataNuo" class="textbox-100 date" value="<?php echo isset($fields['dataNuo']) ? $fields['dataNuo'] : ''; ?>" /></p>
							<p><label class="field" for="dataIki">Paslaugos užsakytos iki</label><input type="text" id="dataIki" name="dataIki" class="textbox-100 date" value="<?php echo isset($fields['dataIki']) ? $fields['dataIki'] : ''; ?>" /></p>
						</fieldset>
						<p><input type="submit" class="submit" name="submit" value="Sudaryti ataskaitą"></p>
					</form>
				</div>
	<?php	} else {
					// išrenkame ataskaitos duomenis
					$servicesData = $servicesObj->getOrderedServices($data['dataNuo'], $data['dataIki']);
					$servicesStats = $servicesObj->getStatsOfOrderedServices($data['dataNuo'], $data['dataIki']);
			
					if(sizeof($servicesData) > 0) { ?>
						<table class="reportTable">
							<tr>
								<th>ID</th>
								<th>Paslauga</th>
								<th>Užsakyta kartų</th>
								<th>Užsakyta už</th>
							</tr>

							<tr>
								<td class="separator" colspan="5"></td>
							</tr>

							<?php
								// suformuojame lentelę
								foreach($servicesData as $key => $val) {
									echo
										"<tr>"
											. "<td>{$val['id']}</td>"
											. "<td>{$val['pavadinimas']}</td>"
											. "<td>{$val['uzsakyta']}</td>"
											. "<td>{$val['bendra_suma']} &euro;</td>"
										. "</tr>";
								}
							?>
							<tr class="aggregate">
								<td></td>
								<td class="label">Suma:</td>
								<td class="border"><?php echo "{$servicesStats[0]['uzsakyta']}"; ?></td>
								<td class="border"><?php echo "{$servicesStats[0]['bendra_suma']}"; ?> &euro;</td>
							</tr>
						</table>

			<?php   } else { ?>
						<div class="warningBox">
							Nurodytu laikotartpiu paslaugų nebuvo užsakyta
						</div>
					<?php
					}
			}
			?>
	</div>
</div>
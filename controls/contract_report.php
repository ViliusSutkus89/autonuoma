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
			<li class="title">Klientų sutarčių ataskaita</li>
			<li>Sudarymo data: <span><?php echo date("Y-m-d"); ?></span></li>
			<li>Sutarčių sudarymo laikotarpis:
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
				<a href="report.php?id=1" title="Nauja ataskaita" class="newReport">nauja ataskaita</a>
			</li>
		</ul>
	</div>
<?php } ?>
<div id="content">
	<div id="contentMain">
		<?php if($formSubmitted == false || $formErrors != null) { ?>
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
		<?php } else {
			
				// išrenkame ataskaitos duomenis
				$contractData = $contractsObj->getCustomerContracts($data['dataNuo'], $data['dataIki']);
				$totalPrice = $contractsObj->getSumPriceOfContracts($data['dataNuo'], $data['dataIki']);
				$totalServicePrice = $contractsObj->getSumPriceOfOrderedServices($data['dataNuo'], $data['dataIki']);
				
				if(sizeof($contractData) > 0) { ?>
		
					<table class="reportTable">
						<tr>
							<th>Sutartis</th>
							<th>Data</th>
							<th>Kaina</th>
							<th>Užsakyta paslaugų už</th>
						</tr>

						<?php

							// suformuojame lentelę
							for($i = 0; $i < sizeof($contractData); $i++) {
								
								if($i == 0 || $contractData[$i]['asmens_kodas'] != $contractData[$i-1]['asmens_kodas']) {
									echo
										"<tr class='rowSeparator'><td colspan='5'></td></tr>"
										. "<tr>"
											. "<td class='groupSeparator' colspan='4'>{$contractData[$i]['vardas']} {$contractData[$i]['pavarde']}</td>"
										. "</tr>";
								}
								
								if($contractData[$i]['sutarties_paslaugu_kaina'] == 0) {
									$contractData[$i]['sutarties_paslaugu_kaina'] = "neužsakyta";
								} else {
									$contractData[$i]['sutarties_paslaugu_kaina'] .= " &euro;";
								}
								
								echo
									"<tr>"
										. "<td>#{$contractData[$i]['nr']}</td>"
										. "<td>{$contractData[$i]['sutarties_data']}</td>"
										. "<td>{$contractData[$i]['sutarties_kaina']} &euro;</td>"
										. "<td>{$contractData[$i]['sutarties_paslaugu_kaina']}</td>"
									. "</tr>";
								if($i == (sizeof($contractData) - 1) || $contractData[$i]['asmens_kodas'] != $contractData[$i+1]['asmens_kodas']) {

									if($contractData[$i]['bendra_kliento_paslaugu_kaina'] == 0) {
										$contractData[$i]['bendra_kliento_paslaugu_kaina'] = "neužsakyta";
									} else {
										$contractData[$i]['bendra_kliento_paslaugu_kaina'] .= " &euro;";
									}
									
									echo 
										"<tr class='aggregate'>"
											. "<td colspan='2'></td>"
											. "<td class='border'>{$contractData[$i]['bendra_kliento_sutarciu_kaina']} &euro;</td>"
											. "<td class='border'>{$contractData[$i]['bendra_kliento_paslaugu_kaina']}</td>"
										. "</tr>";
								}
							}
						?>
						
						<tr class="rowSeparator">
							<td colspan="5"></td>
						</tr>
						
						<tr class="rowSeparator">
							<td colspan="5"></td>
						</tr>
						
						<tr class="aggregate">
							<td class="label" colspan="2">Suma:</td>
							<td class="border"><?php echo $totalPrice[0]['nuomos_suma']; ?> &euro;</td>
							<td class="border">
								<?php
									if($totalServicePrice[0]['paslaugu_suma'] == 0) {
										$totalServicePrice[0]['paslaugu_suma'] = "neužsakyta";
									} else {
										$totalServicePrice[0]['paslaugu_suma'] .= " &euro;";
									}
									
									echo $totalServicePrice[0]['paslaugu_suma'];
								?>
							</td>
						</tr>
					</table>
			<?php   } else { ?>
						<div class="warningBox">
							Nurodytu laikotartpiu sutarčių nebuvo užsakyta
						</div>
					<?php
					}
			} ?>
	</div>
</div>
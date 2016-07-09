<?php require('header.php'); ?>

<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li><a href="<?php echo routing::getURL($module); ?>">Sutartys</a></li>
	<li><?php if(!empty($id)) echo "Sutarties redagavimas"; else echo "Nauja sutartis"; ?></li>
</ul>
<div class="float-clear"></div>

<div id="formContainer">
  <?php require("formErrors.php"); ?>
	<form action="" method="post">
		<fieldset>
			<legend>Sutarties informacija</legend>
			<p>
				<?php if(empty($id)) { ?>
					<label class="field" for="nr">Numeris<?php echo in_array('nr', $required) ? '<span> *</span>' : ''; ?></label>
					<input type="text" id="nr" name="nr" class="textbox-70" value="<?php echo isset($fields['nr']) ? $fields['nr'] : ''; ?>">
				<?php } else { ?>
						<label class="field" for="nr">Numeris</label>
						<span class="input-value"><?php echo $fields['nr']; ?></span>
				<?php } ?>
			</p>
			<p>
				<label class="field" for="sutarties_data">Data<?php echo in_array('sutarties_data', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="sutarties_data" name="sutarties_data" class="date textbox-70" value="<?php echo isset($fields['sutarties_data']) ? $fields['sutarties_data'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="fk_klientas">Klientas<?php echo in_array('fk_klientas', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="fk_klientas" name="fk_klientas">
					<option value="">---------------</option>
					<?php
						// išrenkame klientus
						foreach($customerList as $val) {
							$selected = "";
							if(isset($fields['fk_klientas']) && $fields['fk_klientas'] == $val['asmens_kodas']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['asmens_kodas']}'>{$val['vardas']} {$val['pavarde']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="fk_darbuotojas">Darbuotojas<?php echo in_array('fk_darbuotojas', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="fk_darbuotojas" name="fk_darbuotojas">
					<option value="">---------------</option>
					<?php
						// išrenkame vartotojus
						foreach($employeesList as $val) {
							$selected = "";
							if(isset($fields['fk_darbuotojas']) && $fields['fk_darbuotojas'] == $val['tabelio_nr']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['tabelio_nr']}'>{$val['vardas']} {$val['pavarde']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="nuomos_data_laikas">Nuomos data ir laikas<?php echo in_array('nuomos_data_laikas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="nuomos_data_laikas" name="nuomos_data_laikas" class="datetime textbox-150" value="<?php echo isset($fields['nuomos_data_laikas']) ? $fields['nuomos_data_laikas'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="planuojama_grazinimo_data_laikas">Planuojama grąžinti<?php echo in_array('planuojama_grazinimo_data_laikas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="planuojama_grazinimo_data_laikas" name="planuojama_grazinimo_data_laikas" class="datetime textbox-150" value="<?php echo isset($fields['planuojama_grazinimo_data_laikas']) ? $fields['planuojama_grazinimo_data_laikas'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="faktine_grazinimo_data_laikas">Grąžinta<?php echo in_array('faktine_grazinimo_data_laikas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="faktine_grazinimo_data_laikas" name="faktine_grazinimo_data_laikas" class="datetime textbox-150" value="<?php echo isset($fields['faktine_grazinimo_data_laikas']) ? $fields['faktine_grazinimo_data_laikas'] : ''; ?>">
			</p>
			<p>
				<label class="field" for="busena">Būsena<?php echo in_array('busena', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="busena" name="busena">
					<option value="">---------------</option>
					<?php
						// išrenkame būsenas
						foreach($contractStates as $val) {
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
				<label class="field" for="kaina">Nuomos kaina<?php echo in_array('kaina', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="kaina" name="kaina" class="textbox-70" value="<?php echo isset($fields['kaina']) ? $fields['kaina'] : ''; ?>"> <span class="units">&euro;</span>
			</p>
			<p>
				<label class="field" for="bendra_kaina">Bendra kaina su paslaugomis</label><span class="units"><?php echo isset($fields['bendra_kaina']) ? $fields['bendra_kaina'] . ' &euro;' : ''; ?></span>
				<input type="hidden" name="bendra_kaina" value="<?php echo isset($fields['bendra_kaina']) ? $fields['bendra_kaina'] : ''; ?>" />
			</p>
		</fieldset>

		<fieldset>
			<legend>Automobilio informacija</legend>
			<p>
				<label class="field" for="fk_automobilis">Automobilis<?php echo in_array('fk_automobilis', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="fk_automobilis" name="fk_automobilis">
					<option value="">---------------</option>
					<?php
						// išrenkame automobilius
						foreach($carsList as $val) {
							$selected = "";
							if(isset($fields['fk_automobilis']) && $fields['fk_automobilis'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['valstybinis_nr']} - {$val['marke']} {$val['modelis']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="fk_paemimo_vieta">Paėmimo vieta<?php echo in_array('fk_paemimo_vieta', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="fk_paemimo_vieta" name="fk_paemimo_vieta">
					<option value="">---------------</option>
					<?php
						// išrenkame aikšteles
						foreach($parkingLots as $val) {
							$selected = "";
							if(isset($fields['fk_paemimo_vieta']) && $fields['fk_paemimo_vieta'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['pavadinimas']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="pradine_rida">Rida paimant<?php echo in_array('pradine_rida', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="pradine_rida" name="pradine_rida" class="textbox-70" value="<?php echo isset($fields['pradine_rida']) ? $fields['pradine_rida'] : ''; ?>"><span class="units">km.</span>
			</p>
			<p>
				<label class="field" for="degalu_kiekis_paimant">Degalų kiekis paimant<?php echo in_array('degalu_kiekis_paimant', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="degalu_kiekis_paimant" name="degalu_kiekis_paimant" class="textbox-70" value="<?php echo isset($fields['degalu_kiekis_paimant']) ? $fields['degalu_kiekis_paimant'] : ''; ?>"><span class="units">l.</span>
			</p>
			<p>
				<label class="field" for="fk_grazinimo_vieta">Grąžinimo vieta<?php echo in_array('fk_grazinimo_vieta', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="fk_grazinimo_vieta" name="fk_grazinimo_vieta">
					<option value="">---------------</option>
					<?php
						// išrenkame aikšteles
						foreach($parkingLots as $val) {
							$selected = "";
							if(isset($fields['fk_grazinimo_vieta']) && $fields['fk_grazinimo_vieta'] == $val['id']) {
								$selected = " selected='selected'";
							}
							echo "<option{$selected} value='{$val['id']}'>{$val['pavadinimas']}</option>";
						}
					?>
				</select>
			</p>
			<p>
				<label class="field" for="galine_rida">Rida grąžinus<?php echo in_array('galine_rida', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="galine_rida" name="galine_rida" class="textbox-70" value="<?php echo isset($fields['galine_rida']) ? $fields['galine_rida'] : ''; ?>"><span class="units">km.</span>
			</p>
			<p>
				<label class="field" for="dagalu_kiekis_grazinus">Degalų kiekis grąžinus<?php echo in_array('dagalu_kiekis_grazinus', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="dagalu_kiekis_grazinus" name="dagalu_kiekis_grazinus" class="textbox-70" value="<?php echo isset($fields['dagalu_kiekis_grazinus']) ? $fields['dagalu_kiekis_grazinus'] : ''; ?>"><span class="units">l.</span>
			</p>
		</fieldset>

		<fieldset>
			<legend>Papildomos paslaugos</legend>
			<div class="childRowContainer">
				<div class="labelLeft wide<?php if(empty($fields['uzsakytos_paslaugos'])) echo ' hidden'; ?>">Paslauga</div>
				<div class="labelRight<?php if(empty($fields['uzsakytos_paslaugos'])) echo ' hidden'; ?>">Kiekis</div>
				<div class="float-clear"></div>
				<?php
					if(empty($fields['uzsakytos_paslaugos'])) {
				?>
					<div class="childRow hidden">
						<select class="elementSelector" name="paslaugos[]" disabled="disabled">
								<?php
                $lastService = "";
                foreach($services as $service) {
                  if ($lastService != $service['id']) {
                    if ($lastService != "") {
                      echo "</optgroup>\n";
                    }
                    $lastService = $service['id'];
										echo "<optgroup label='{$service['pavadinimas']}'>\n";
                  }
                  if ($service['kaina']) {
                    echo "<option value='{$service['id']}:{$service['kaina']}:{$service['galioja_nuo']}'>",
                      "{$service['pavadinimas']} {$service['kaina']} EUR (nuo {$service['galioja_nuo']})</option>\n";
                  }
                }
                if ($lastService != "") {
                  echo "</optgroup>\n";
                }
								?>
						</select>
						<input type="text" name="kiekiai[]" class="textbox-30" value="" disabled="disabled" />
						<a href="#" title="" class="removeChild">šalinti</a>
					</div>
					<div class="float-clear"></div>

				<?php
					} else {
						foreach($fields['uzsakytos_paslaugos'] as $val) {
				?>
						<div class="childRow">
							<select class="elementSelector" name="paslaugos[]">
								<?php
                $lastService = "";
                foreach($services as $service) {
                  if ($lastService != $service['id']) {
                    if ($lastService != "") {
                      echo "</optgroup>\n";
                    }
                    $lastService = $service['id'];
										echo "<optgroup label='{$service['pavadinimas']}'>\n";
                  }
									$selected = "";
									if($val['fk_kaina_galioja_nuo'] == $service['galioja_nuo'] && $val['fk_paslauga'] == $service['fk_paslauga']) {
										$selected = " selected='selected'";
									}
                  echo "<option{$selected} value='{$service['id']}:{$service['kaina']}:{$service['galioja_nuo']}'>",
                    "{$service['pavadinimas']} {$service['kaina']} EUR (nuo {$service['galioja_nuo']})</option>\n";
                }
                if ($lastService != "") {
                  echo "</optgroup>\n";
                }
								?>
							</select>
							<input type="text" name="kiekiai[]" class="textbox-30" value="<?php echo isset($val['kiekis']) ? $val['kiekis'] : ''; ?>" />
							<a href="#" title="" class="removeChild">šalinti</a>
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
		<p>
			<input type="submit" class="submit" name="submit" value="Išsaugoti">
		</p>
	</form>
</div>
<?php require('footer.php'); ?>

<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li><a href="<?php echo routing::getURL($module); ?>">Automobiliai</a></li>
	<li><?php if(!empty($id)) echo "Automobilio redagavimas"; else echo "Naujas automobilis"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
  <?php require("formErrors.php"); ?>
	<form action="" method="post">
		<fieldset>
			<legend>Automobilio informacija</legend>
			<p>
				<label class="field" for="modelis">Modelis<?php echo in_array('modelis', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="modelis" name="modelis">
					<option value="-1">---------------</option>
					<?php
            $lastBrand = "";
            foreach($brandsModels as $model) {
              if ($lastBrand != $model['marke']) {
                if ($lastBrand != "") {
                  echo "</optgroup>\n";
                }
                $lastBrand = $model['marke'];
								echo "<optgroup label='{$model['marke']}'>\n";
              }

              if ($model['modelis']) {
								$selected = "";
                if (!empty($fields['modelis']) && $fields['modelis'] == $model['id']) {
									$selected = " selected='selected'";
								}
								echo "<option{$selected} value='{$model['id']}'>{$model['modelis']}</option>\n";
              }
            }
            if ($lastBrand != "") {
              echo "</optgroup>\n";
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
						foreach($luggage as $key => $val) {
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
	</form>
</div>

<?php
require('footer.php');


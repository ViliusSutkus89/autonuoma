<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li><a href="<?php echo routing::getURL($module); ?>">Automobilių modeliai</a></li>
	<li><?php if(!empty($id)) echo "Modelio redagavimas"; else echo "Naujas modelis"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
  <?php require("formErrors.php"); ?>
	<form action="" method="post">
		<fieldset>
			<legend>Modelio informacija</legend>
			<p>
				<label class="field" for="brand">Markė<?php echo in_array('fk_marke', $required) ? '<span> *</span>' : ''; ?></label>
				<select id="brand" name="fk_marke">
					<option value="-1">Pasirinkite markę</option>
					<?php
						// išrenkame visas markes
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
	</form>
</div>

<?php
require('footer.php');


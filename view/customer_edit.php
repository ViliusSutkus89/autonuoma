<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Klientai</a></li>
	<li><?php if(!empty($id)) echo "Kliento redagavimas"; else echo "Naujas klientas"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
  <?php require("formErrors.php"); ?>
	<form action="" method="post">
		<fieldset>
			<legend>Kliento informacija</legend>
				<p>
					<label class="field" for="asmens_kodas">Asmens kodas<?php echo in_array('asmens_kodas', $required) ? '<span> *</span>' : ''; ?></label>
					<?php if(!isset($fields['editing'])) { ?>
						<input type="text" id="asmens_kodas" name="asmens_kodas" class="textbox-150" value="<?php echo isset($fields['asmens_kodas']) ? $fields['asmens_kodas'] : ''; ?>" />
						<?php if(key_exists('asmens_kodas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['asmens_kodas']} simb.)</span>"; ?>
					<?php } else { ?>
						<span class="input-value"><?php echo $fields['asmens_kodas']; ?></span>
						<input type="hidden" name="editing" value="1" />
						<input type="hidden" name="asmens_kodas" value="<?php echo $fields['asmens_kodas']; ?>" />
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
			<p>
				<label class="field" for="gimimo_data">Gimimo data<?php echo in_array('gimimo_data', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="gimimo_data" name="gimimo_data" class="textbox-70 date" value="<?php echo isset($fields['gimimo_data']) ? $fields['gimimo_data'] : ''; ?>" />
			</p>
			<p>
				<label class="field" for="telefonas">Telefonas<?php echo in_array('telefonas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="telefonas" name="telefonas" class="textbox-150" value="<?php echo isset($fields['telefonas']) ? $fields['telefonas'] : ''; ?>" />
			</p>
			<p>
				<label class="field" for="epastas">Elektroninis paštas<?php echo in_array('epastas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="epastas" name="epastas" class="textbox-150" value="<?php echo isset($fields['epastas']) ? $fields['epastas'] : ''; ?>" />
			</p>
		</fieldset>
		<p class="required-note">* pažymėtus laukus užpildyti privaloma</p>
		<p>
			<input type="submit" class="submit" name="submit" value="Išsaugoti">
		</p>
	</form>
</div>
<?php require('footer.php'); ?>

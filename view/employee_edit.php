<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="index.php">Pradžia</a></li>
	<li><a href="index.php?module=<?php echo $module; ?>">Darbuotojai</a></li>
	<li><?php if(!empty($id)) echo "Darbuotojo redagavimas"; else echo "Naujas darbuotojas"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
  <?php require("formErrors.php"); ?>
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
<?php
require('footer.php');


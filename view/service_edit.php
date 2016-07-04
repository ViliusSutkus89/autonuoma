<?php require('header.php'); ?>
<ul id="pagePath">
	<li><a href="<?php echo routing::getURL(); ?>">Pradžia</a></li>
	<li><a href="<?php echo routing::getURL($module); ?>">Papildomos paslaugos</a></li>
	<li><?php if(!empty($id)) echo "Paslaugos redagavimas"; else echo "Nauja paslauga"; ?></li>
</ul>
<div class="float-clear"></div>
<div id="formContainer">
  <?php require("formErrors.php"); ?>
	<form action="" method="post">
		<fieldset>
			<legend>Papildomos paslaugos informacija</legend>
			<p>
				<label class="field" for="pavadinimas">Pavadinimas<?php echo in_array('pavadinimas', $required) ? '<span> *</span>' : ''; ?></label>
				<input type="text" id="pavadinimas" name="pavadinimas" class="textbox-150" value="<?php echo isset($fields['pavadinimas']) ? $fields['pavadinimas'] : ''; ?>">
				<?php if(key_exists('pavadinimas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['pavadinimas']} simb.)</span>"; ?>
			</p>
			<p>
				<label class="field" for="aprasymas">Aprašymas<?php echo in_array('aprasymas', $required) ? '<span> *</span>' : ''; ?></label>
				<textarea id="aprasymas" name="aprasymas" class=""><?php echo isset($fields['aprasymas']) ? $fields['aprasymas'] : ''; ?></textarea>
				<?php if(key_exists('aprasymas', $maxLengths)) echo "<span class='max-len'>(iki {$maxLengths['aprasymas']} simb.)</span>"; ?>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>Paslaugos kainos</legend>
			<div class="childRowContainer">
				<div class="labelLeft<?php if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) echo ' hidden'; ?>">Kaina</div>
				<div class="labelRight<?php if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) echo ' hidden'; ?>">Galioja nuo</div>
				<div class="float-clear"></div>
				<?php
					if(empty($fields['paslaugos_kainos']) || sizeof($fields['paslaugos_kainos']) == 0) {
				?>
					
					<div class="childRow hidden">
						<input type="text" name="kainos[]" value="" class="textbox-70" disabled="disabled" />
						<input type="text" name="datos[]" value="" class="textbox-70" disabled="disabled" />
						<input type="hidden" class="isDisabledForEditing" name="neaktyvus[]" value="0" />
						<a href="#" title="" class="removeChild">šalinti</a>
					</div>
					<div class="float-clear"></div>
					
				<?php
					} else {
						foreach($fields['paslaugos_kainos'] as $key => $val) {
				?>
							<div class="childRow">
								<input type="text" name="kainos[]" value="<?php echo $val['kaina']; ?>" class="textbox-70<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo ' disabledInput'; ?>" />
								<input type="text" name="datos[]" value="<?php echo $val['galioja_nuo']; ?>" class="textbox-70<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo ' disabledInput'; ?> date" />
								<input type="hidden" class="isDisabledForEditing" name="neaktyvus[]" value="<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo "1"; else echo "0"; ?>" />
								<a href="#" title="" class="removeChild<?php if(isset($val['neaktyvus']) && $val['neaktyvus'] == 1) echo " hidden"; ?>">šalinti</a>
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
		
		<p class="required-note">* pažymėtus laukus užpildyti privaloma</p>
		<p>
			<input type="submit" class="submit" name="submit" value="Išsaugoti">
		</p>
		<?php if(isset($fields['id'])) { ?>
			<input type="hidden" name="id" value="<?php echo $fields['id']; ?>" />
		<?php } ?>
	</form>
</div>
<?php
require('footer.php');


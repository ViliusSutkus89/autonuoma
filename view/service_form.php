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
				<div class="labelLeft<?php if(empty($prices)) echo ' hidden'; ?>">Kaina</div>
				<div class="labelRight<?php if(empty($prices)) echo ' hidden'; ?>">Galioja nuo</div>
				<div class="float-clear"></div>
				<?php
					if(empty($prices)) {
				?>
					<div class="childRow hidden">
						<input type="text" name="kaina[]" value="" class="textbox-70" disabled="disabled" />
						<input type="text" name="galioja_nuo[]" value="" class="textbox-70 date" disabled="disabled" />
						<a href="#" title="" class="removeChild">šalinti</a>
					</div>
					<div class="float-clear"></div>
				<?php
					} else {
            foreach ($prices as $price) {
              $d = (!empty($price['naudojama_uzsakymuose'])) ? 'disabled="disabled"' : '';
				?>
							<div class="childRow">
								<input type="text" name="kaina[]" value="<?php echo $price['kaina']; ?>" class="textbox-70" <?php echo $d; ?> />
								<input type="text" name="galioja_nuo[]" value="<?php echo $price['galioja_nuo']; ?>" class="textbox-70 date" <?php echo $d; ?> />
								<a href="#" title="" class="removeChild<?php if($d) echo " hidden"; ?>">šalinti</a>
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
	</form>
</div>
<?php
require('footer.php');


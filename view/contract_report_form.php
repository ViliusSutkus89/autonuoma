<?php require('header_report.php'); ?>

<div id="content">
	<div id="contentMain">
			<div id="formContainer">
        <?php require("formErrors.php"); ?>
				<form action="" method="post">
					<fieldset>
						<legend>Įveskite ataskaitos kriterijus</legend>
						<p><label class="field" for="dataNuo">Sutartys sudarytos nuo</label><input type="text" id="dataNuo" name="dataNuo" class="textbox-100 date" value="<?php echo isset($fields['dataNuo']) ? $fields['dataNuo'] : ''; ?>" /></p>
						<p><label class="field" for="dataIki">Sutartys sudarytos iki</label><input type="text" id="dataIki" name="dataIki" class="textbox-100 date" value="<?php echo isset($fields['dataIki']) ? $fields['dataIki'] : ''; ?>" /></p>
					</fieldset>
					<p><input type="submit" class="submit" name="submit" value="Sudaryti ataskaitą"></p>
				</form>
			</div>
	</div>
</div>

<?php require('footer_report.php'); ?>


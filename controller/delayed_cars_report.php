<?php
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';

$contractsObj = new contracts();
	
$formErrors = null;
$fields = array();
$data = array();

$template->setView("delayed_cars_report_form");

if(!empty($_POST['submit'])) {
  // nustatome laukų validatorių tipus
	$validations = array (
		'dataNuo' => 'date',
    'dataIki' => 'date'
  );
		
	// sukuriame validatoriaus objektą
	$validator = new validator($validations);
	if($validator->validate($_POST)) {
		// suformuojame laukų reikšmių masyvą SQL užklausai
		$data = $validator->preparePostFieldsForSQL();

    // išrenkame ataskaitos duomenis
    $delayedCarsData = $contractsObj->getDelayedCars($data['dataNuo'], $data['dataIki']);
    $template->assign("delayedCarsData", $delayedCarsData);
    $template->setView("delayed_cars_report");
	} else {
		// gauname klaidų pranešimą
		$formErrors = $validator->getErrorHTML();
		// gauname įvestus laukus
		$fields = $_POST;
	}
}

$template->assign("formErrors", $formErrors);
$template->assign("fields", $fields);
$template->assign("data", $data);


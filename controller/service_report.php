<?php
require_once 'utils/validator.class.php';
require_once 'model/services.class.php';

$servicesObj = new services();

$formErrors = null;
$fields = array();
$data = array();

$template->setView("service_report_form");

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
    $servicesData = $servicesObj->getOrderedServices($data['dataNuo'], $data['dataIki']);
    $servicesStats = $servicesObj->getStatsOfOrderedServices($data['dataNuo'], $data['dataIki']);

    $template->assign("servicesData", $servicesData);
    $template->assign("servicesStats", $servicesStats);

    $template->setView("service_report");
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


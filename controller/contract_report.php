<?php
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';

$contractsObj = new contracts();
	
$formErrors = null;
$fields = array();
$data = array();

$template->setView("contract_report_form");

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
    $contractData = $contractsObj->getCustomerContracts($data['dataNuo'], $data['dataIki']);
    $totalPrice = $contractsObj->getSumPriceOfContracts($data['dataNuo'], $data['dataIki']);
    $totalServicePrice = $contractsObj->getSumPriceOfOrderedServices($data['dataNuo'], $data['dataIki']);

    $template->assign("contractData", $contractData);
    $template->assign("totalPrice", $totalPrice);
    $template->assign("totalServicePrice", $totalServicePrice);

    $template->setView("contract_report");

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


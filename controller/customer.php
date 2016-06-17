<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/customers.class.php';

$customersObj = new customers();

if (!empty($id) || $action == 'new') {
  $formErrors = null;
  $fields = array();
  
  // nustatome privalomus formos laukus
  $required = array('asmens_kodas', 'vardas', 'pavarde', 'gimimo_data', 'telefonas');

  // maksimalūs leidžiami laukų ilgiai
  $maxLengths = array (
    'asmens_kodas' => 11,
    'vardas' => 20,
    'pavarde' => 20
  );

	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {

		// nustatome laukų validatorių tipus
		$validations = array (
			'asmens_kodas' => 'positivenumber',
			'vardas' => 'alfanum',
			'pavarde' => 'alfanum',
			'gimimo_data' => 'date',
			'telefonas' => 'phone',
			'epastas' => 'email'
		);
		
		// sukuriame laukų validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();

			if(isset($data['editing'])) {
				// redaguojame klientą
				$customersObj->updateCustomer($data);
			} else {
				// įrašome naują klientą
				$customersObj->insertCustomer($data);
			}

			// nukreipiame vartotoją į klientų puslapį
			header("Location: index.php?module={$module}");
      $template->disableRendering();
		}
		else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			// išrenkame klientą
			$fields = $customersObj->getCustomer($id);
			$fields['editing'] = 1;
		}
	}
  
  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);
  
  $template->assign('formErrors', $formErrors);
  
  $template->setView("customer_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar klientas neturi sudarytų sutarčių
  $count = $customersObj->getContractCountOfCustomer($removeId);
  
  $removeErrorParameter = '';
  if($count == 0) {
  	// šaliname klientą
  	$customersObj->deleteCustomer($removeId);
  } else {
  	// nepašalinome, nes klientas sudaręs bent vieną sutartį, rodome klaidos pranešimą
  	$removeErrorParameter = '&remove_error=1';
  }
  
  // nukreipiame į klientų puslapį
  header("Location: index.php?module={$module}{$removeErrorParameter}");
  $template->disableRendering();
}

// View list
else {
  // suskaičiuojame bendrą įrašų kiekį
	$elementCount = $customersObj->getCustomersListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio klientus
  $data = $customersObj->getCustomersList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("customer_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}


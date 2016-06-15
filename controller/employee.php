<?php
require 'utils/paging.class.php';
require 'utils/validator.class.php';
require 'model/employees.class.php';

// sukuriame darbuotojų klasės objektą
$employeesObj = new employees();

if (!empty($id) || $action == 'new') {
	$formErrors = null;
	$fields = array();
	
	// nustatome privalomus formos laukus
	$required = array('tabelio_nr', 'vardas', 'pavarde');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'tabelio_nr' => 6,
		'vardas' => 20,
		'pavarde' => 20
	);

	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {
		
		// nustatome laukų validatorių tipus
		$validations = array (
			'tabelio_nr' => 'alfanum',
			'vardas' => 'alfanum',
			'pavarde' => 'alfanum');
		
		// sukuriame laukų validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();

			if(isset($data['editing'])) {
				// redaguojame klientą
				$employeesObj->updateEmployee($data);
			} else {
				// įrašome naują klientą
				$employeesObj->insertEmployee($data);
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
			$fields = $employeesObj->getEmployee($id);
			$fields['editing'] = 1;
		}
	}

  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);

  $template->assign('formErrors', $formErrors);

  $template->setView("employee_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar darbuotojas neturi sudarytų sutarčių
  $count = $employeesObj->getContractCountOfEmployee($removeId);
  
  $removeErrorParameter = '';
  if($count == 0) {
  	// šaliname darbuotoją
  	$employeesObj->deleteEmployee($removeId);
  } else {
  	// nepašalinome, nes darbuotojas sudaręs bent vieną sutartį, rodome klaidos pranešimą
  	$removeErrorParameter = '&remove_error=1';
  }
  
  // nukreipiame į darbuotojų puslapį
  header("Location: index.php?module={$module}{$removeErrorParameter}");
  $template->disableRendering();
}

// View list
else {
	// suskaičiuojame bendrą įrašų kiekį
	$elementCount = $employeesObj->getEmplyeesListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

	// išrenkame nurodyto puslapio darbuotojus
	$data = $employeesObj->getEmplyeesList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("employee_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}


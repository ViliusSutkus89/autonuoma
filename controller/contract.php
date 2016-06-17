<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/cars.class.php';
require_once 'model/contracts.class.php';
require_once 'model/customers.class.php';
require_once 'model/employees.class.php';
require_once 'model/services.class.php';

// sukuriame sutarčių klasės objektą
$contractsObj = new contracts();


if (!empty($id) || $action == 'new') {
  $servicesObj = new services();
  $carsObj = new cars();
  $employeesObj = new employees();
  $customersObj = new customers();
	$fields = array();

	$formErrors = null;

	// nustatome privalomus laukus
	$required = array('nr', 'sutarties_data', 'nuomos_data_laikas', 'planuojama_grazinimo_data_laikas', 'pradine_rida', 'kaina', 'degalu_kiekis_paimant', 'busena', 'fk_klientas', 'fk_darbuotojas', 'fk_automobilis', 'fk_grazinimo_vieta', 'fk_paemimo_vieta', 'kiekiai');

	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {
	  // nustatome laukų validatorių tipus
	  $validations = array (
	  	'nr' => 'positivenumber',
	  	'sutarties_data' => 'date',
	  	'nuomos_data_laikas' => 'datetime',
	  	'planuojama_grazinimo_data_laikas' => 'datetime',
	  	'faktine_grazinimo_data_laikas' => 'datetime',
	  	'pradine_rida' => 'int',
	  	'galine_rida' => 'int',
	  	'kaina' => 'price',
	  	'degalu_kiekis_paimant' => 'int',
	  	'dagalu_kiekis_grazinus' => 'int',
	  	'busena' => 'positivenumber',
	  	'fk_klientas' => 'alfanum',
	  	'fk_darbuotojas' => 'alfanum',
	  	'fk_automobilis' => 'positivenumber',
	  	'fk_grazinimo_vieta' => 'positivenumber',
	  	'fk_paemimo_vieta' => 'positivenumber',
      'kiekiai' => 'int'
    );
		// sukuriame laukų validatoriaus objektą
		$validator = new validator($validations, $required);
		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();

			if(isset($data['editing'])) {
				// atnaujiname sutartį
				$contractsObj->updateContract($data);
				// atnaujiname užsakytas paslaugas
				$contractsObj->updateOrderedServices($data);
			} else {
				// patikriname, ar nėra sutarčių su tokiu pačiu numeriu
				$exists = $contractsObj->getContract($data['nr']);
				if($exists) {
					// sudarome klaidų pranešimą
					$formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
					// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
					$fields = $_POST;
				} else {
					// įrašome naują sutartį
					$contractsObj->insertContract($data);
					// įrašome užsakytas paslaugas
					$contractsObj->updateOrderedServices($data);
				}
			}
			
			// nukreipiame vartotoją į sutarčių puslapį
			if($formErrors == null) {
				header("Location: index.php?module={$module}");
        $template->disableRendering();
			}

		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$fields = $_POST;
			if(isset($_POST['kiekiai']) && sizeof($_POST['kiekiai']) > 0) {
				$i = 0;
				foreach($_POST['kiekiai'] as $key => $val) {
					$fields['uzsakytos_paslaugos'][$i]['kiekis'] = $val;
					$i++;
				}
			}
		}
  }
  
	if(!empty($id)) {
	// tikriname, ar adreso eilutėje nenurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		$fields = $contractsObj->getContract($id);
		$fields['uzsakytos_paslaugos'] = $contractsObj->getOrderedServices($id);
		$fields['editing'] = 1;
	}
  $template->assign('fields', $fields);
  $template->assign('required', $required);

  $template->assign('customerList', $customersObj->getCustomersList());
  $template->assign('employeesList', $employeesObj->getEmplyeesList());
  $template->assign('contractStates', $contractsObj->getContractStates());
  $template->assign('carsList', $carsObj->getCarList());
  $template->assign('parkingLots', $contractsObj->getParkingLots());

  if (!empty($formErrors))
    $template->assign('formErrors', $formErrors);

  $servicesList = $servicesObj->getServicesList();

  $serviceIDs = array();
	foreach($servicesList as $val)
    $serviceIDs[] = $val['id'];

  $servicePrices = $servicesObj->getServicePrices($serviceIDs);

  $template->assign('servicesList', $servicesList);
  $template->assign('servicePrices', $servicePrices);

  $template->setView("contract_edit");
}

else if(!empty($removeId)) {
	// pašaliname užsakytas paslaugas
	$contractsObj->deleteOrderedServices($removeId);

	// šaliname sutartį
	$contractsObj->deleteContract($removeId);

	// nukreipiame į sutarčių puslapį
	header("Location: index.php?module={$module}");
  $template->disableRendering();
}

// View list
else {
  // suskaičiuojame bendrą įrašų kiekį
  $elementCount = $contractsObj->getContractListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio sutartis
  $data = $contractsObj->getContractList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("contract_list");
}


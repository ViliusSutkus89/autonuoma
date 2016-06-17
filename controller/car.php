<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/cars.class.php';
require_once 'model/brands.class.php';
require_once 'model/models.class.php';

// sukuriame automobilių klasės objektą
$carsObj = new cars();

if (!empty($id) || $action == 'new') {
	$formErrors = null;
	$fields = array();

	$brandsObj = new brands();
	$modelsObj = new models();

	// nustatome privalomus laukus
	$required = array('modelis', 'valstybinis_nr', 'pagaminimo_data', 'pavaru_deze', 'degalu_tipas', 'kebulas', 'bagazo_dydis', 'busena', 'rida', 'vietu_skaicius', 'registravimo_data', 'verte');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array (
		'valstybinis_nr' => 6
	);

	// vartotojas paspaudė išsaugojimo mygtuką
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array (
			'modelis' => 'positivenumber',
			'valstybinis_nr' => 'alfanum',
			'pavaru_deze' => 'positivenumber',
			'degalu_tipas' => 'positivenumber',
			'kebulas' => 'positivenumber',
			'bagazo_dydis' => 'positivenumber',
			'busena' => 'positivenumber',
			'pagaminimo_data' => 'date',
			'rida' => 'positivenumber',
			'vietu_skaicius' => 'positivenumber',
			'registravimo_data' => 'date',
			'verte' => 'price'
			);
				
		// sukuriame laukų validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			
			// sutvarkome checkbox reikšmes
      $data['radijas'] = (!empty($data['radijas']) && $data['radijas'] == 'on') ? 1 : 0;

      $data['grotuvas'] = (!empty($data['grotuvas']) && $data['grotuvas'] == 'on') ? 1 : 0;

		  $data['kondicionierius'] =  (!empty($data['kondicionierius']) && $data['kondicionierius'] == 'on') ? 1 : 0;
			
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$carsObj->updateCar($data);
			} else {
				// randame didžiausią automobilio id duomenų bazėje
				$latestId = $carsObj->getMaxIdOfCar();

				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$carsObj->insertCar($data);
			}
			
			// nukreipiame vartotoją į automobilių puslapį
			header("Location: index.php?module={$module}");
      $template->disableRendering();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			// išrenkame automobilį
			$fields = $carsObj->getCar($id);
		}
	}

  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);

  $template->assign('formErrors', $formErrors);

	$brands = $brandsObj->getBrandList();

  $brandIDs = array();
	foreach($brands as $val)
    $brandIDs[] = $val['id'];
  $models = $modelsObj->getModelsListByBrands($brandIDs);

  $template->assign('brands', $brands);
  $template->assign('models', $models);

  $gearboxes = $carsObj->getGearboxList();
  $fueltypes = $carsObj->getFuelTypeList();
  $bodytypes = $carsObj->getBodyTypeList();
  $luggage = $carsObj->getLuggageTypeList();
  $car_states = $carsObj->getCarStateList();

  $template->assign('gearboxes', $gearboxes);
  $template->assign('fueltypes', $fueltypes);
  $template->assign('bodytypes', $bodytypes);
  $template->assign('luggage', $luggage);
  $template->assign('car_states', $car_states);

  $template->setView("car_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar automobilis neįtrauktas į sutartis
  $count = $carsObj->getContractCountOfCar($removeId);
  
  $removeErrorParameter = '';
  if($count == 0) {
    // šaliname automobilį
    $carsObj->deleteCar($removeId);
  } else {
    // nepašalinome, nes automobilis įtrauktas bent į vieną sutartį, rodome klaidos pranešimą
    $removeErrorParameter = '&remove_error=1';
  }
  
  // nukreipiame į automobilių puslapį
  header("Location: index.php?module={$module}{$removeErrorParameter}");
  $template->disableRendering();
}

// View list
else {
  // suskaičiuojame bendrą įrašų kiekį
  $elementCount = $carsObj->getCarListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio automobilius
  $data = $carsObj->getCarList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("car_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}


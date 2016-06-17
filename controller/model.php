<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/models.class.php';
require_once 'model/brands.class.php';

// sukuriame modelių klasės objektą
$modelsObj = new models();
if (!empty($id) || $action == 'new') {

	$formErrors = null;
	$fields = array();

	$brandsObj = new brands();

	// nustatome privalomus laukus
	$required = array('pavadinimas', 'fk_marke');

	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array ('pavadinimas' => 20);

	// paspaustas išsaugojimo mygtukas
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array (
			'pavadinimas' => 'anything',
			'fk_marke' => 'positivenumber');
		
		// sukuriame validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);
		
		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$modelsObj->updateModel($data);
			} else {
				// randame didžiausią modelio id duomenų bazėje
				$latestId = $modelsObj->getMaxIdOfModel();

				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$modelsObj->insertModel($data);
			}
			
			// nukreipiame į modelių puslapį
			header("Location: index.php?module={$module}");
      $template->disableRendering();
		} else {
			// gauname klaidų pranešimą
			$formErrors = $validator->getErrorHTML();
			// gauname įvestus laukus
			$fields = $_POST;
		}
	} else {
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
		if(!empty($id)) {
			$fields = $modelsObj->getModel($id);
		}
	}

  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);
  $template->assign('formErrors', $formErrors);

  $brands = $brandsObj->getBrandList();
  $template->assign('brands', $brands);

  $template->setView("model_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar šalinamas modelis nenaudojamas, t.y. nepriskirtas jokiam automobiliui
  $count = $modelsObj->getCarCountOfModel($removeId);
  
  $removeErrorParameter = '';
  if($count == 0) {
  	// pašaliname modelį
  	$modelsObj->deleteModel($removeId);
  } else {
  	// nepašalinome, nes modelis priskirtas bent vienam automobiliui, rodome klaidos pranešimą
  	$removeErrorParameter = '&remove_error=1';
  }
  
  // nukreipiame į modelių puslapį
  header("Location: index.php?module={$module}{$removeErrorParameter}");
  $template->disableRendering();
}

// View list
else {
  // suskaičiuojame bendrą įrašų kiekį
  $elementCount = $modelsObj->getModelListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio modelius
  $data = $modelsObj->getModelList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("model_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}


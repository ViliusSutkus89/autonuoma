<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/brands.class.php';

// sukuriame markių klasės objektą
$brandsObj = new brands();

if (!empty($id) || $action == 'new') {
	$formErrors = null;
	$fields = array();

	// nustatome privalomus laukus
	$required = array('pavadinimas');
	
	// maksimalūs leidžiami laukų ilgiai
	$maxLengths = array ('pavadinimas' => 20);

	// paspaustas išsaugojimo mygtukas
	if(!empty($_POST['submit'])) {
		// nustatome laukų validatorių tipus
		$validations = array ('pavadinimas' => 'anything');

		// sukuriame validatoriaus objektą
		$validator = new validator($validations, $required, $maxLengths);

		if($validator->validate($_POST)) {
			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			if(isset($data['id'])) {
				// atnaujiname duomenis
				$brandsObj->updateBrand($data);
			} else {
				// randame didžiausią markės id duomenų bazėje
				$latestId = $brandsObj->getMaxIdOfBrand();

				// įrašome naują įrašą
				$data['id'] = $latestId + 1;
				$brandsObj->insertBrand($data);
			}

			// nukreipiame į markių puslapį
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
			$fields = $brandsObj->getBrand($id);
		}
	}

  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);

  $template->assign('formErrors', $formErrors);

  $template->setView("brand_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar šalinama markė nepriskirta modeliui
  $count = $brandsObj->getModelCountOfBrand($removeId);
  
  $removeErrorParameter = '';
  if($count == 0) {
  	// šaliname markę
  	$brandsObj->deleteBrand($removeId);
  } else {
  	// nepašalinome, nes markė priskirta modeliui, rodome klaidos pranešimą
  	$removeErrorParameter = '$remove_error=1';
  }
  // nukreipiame į markių puslapį
  header("Location: index.php?module={$module}{$removeErrorParameter}");
  $template->disableRendering();
}

// View list
else {
  // suskaičiuojame bendrą įrašų kiekį
  $elementCount = $brandsObj->getBrandListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio markes
  $data = $brandsObj->getBrandList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("brand_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}


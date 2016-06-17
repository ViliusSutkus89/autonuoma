<?php
require 'utils/paging.class.php';
require 'utils/validator.class.php';
require 'model/contracts.class.php';
require 'model/services.class.php';

$contractsObj = new contracts();
$servicesObj = new services();
	
if (!empty($id) || $action == 'new') {
  $formErrors = null;
  $fields = array();
  
  // nustatome privalomus laukus
  $required = array('pavadinimas', 'kainos', 'datos');
  
  // maksimalūs leidžiami laukų ilgiai
  $maxLengths = array (
  	'pavadinimas' => 40,
  	'aprasymas' => 300
  );
  
  // paspaustas išsaugojimo mygtukas
  if(!empty($_POST['submit'])) {
  	// nustatome laukų validatorių tipus
  	$validations = array (
  		'pavadinimas' => 'anything',
  		'aprasymas' => 'anything',
  		'kainos' => 'price',
      'datos' => 'date'
    );
  	
  	// sukuriame validatoriaus objektą
  	$validator = new validator($validations, $required, $maxLengths);
  	
  	// laukai įvesti be klaidų
  	if($validator->validate($_POST)) {
  		// suformuojame laukų reikšmių masyvą SQL užklausai
  		$data = $validator->preparePostFieldsForSQL();
  		if(isset($data['id'])) {
  			// atnaujiname duomenis
  			$servicesObj->updateService($data);
  			
  			// pašaliname paslaugos kainas, kurios nėra naudojamos sutartyse
        $galiojaNuo = array();
  			foreach($data['kainos'] as $key=>$val) {
  				if($data['neaktyvus'][$key] == 1) {
            $galiojaNuo[] = $data['datos'][$key];

  				}
  			}
        $servicesObj->deleteServicePrices($data['id'], $galiojaNuo);
  			
  			// atnaujiname paslaugos kainas, kurios nėra naudojamos sutartyse
  			$servicesObj->insertServicePrices($data);
  		} else {
  			// randame didžiausią paslaugos numeri duomenų bazėje
  			$latestId = $servicesObj->getMaxIdOfService();
  			
  			// įrašome naują įrašą
  			$data['id'] = $latestId + 1;
  			$servicesObj->insertService($data);
  
  			// įrašome paslaugų kainas
  			$servicesObj->insertServicePrices($data);
  		}
  		
  		// nukreipiame į modelių puslapį
  		header("Location: index.php?module={$module}");
      $template->disableRendering();
  	} else {
  		// gauname klaidų pranešimą
  		$formErrors = $validator->getErrorHTML();
  		// gauname įvestus laukus
  		$fields = $_POST;
  		if(isset($_POST['kainos']) && sizeof($_POST['kainos']) > 0) {
  			$i = 0;
  			foreach($_POST['kainos'] as $key => $val) {
  				$fields['paslaugos_kainos'][$i]['kaina'] = $val;
  				$fields['paslaugos_kainos'][$i]['galioja_nuo'] = $_POST['datos'][$key];
  				$fields['paslaugos_kainos'][$i]['neaktyvus'] = $_POST['neaktyvus'][$key];
  				$i++;
  			}
  		}
  	}
  } else {
  	// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
  	if(!empty($id)) {
  		$fields = $servicesObj->getService($id);
  		$tmp = $servicesObj->getServicePrices($id);
  		if(sizeof($tmp) > 0) {
  			foreach($tmp as $key => $val) {
  				// jeigu paslaugos kaina yra naudojama, jos koreguoti neleidziame ir įvedimo laukelį padarome neaktyvų
  				$priceCount = $contractsObj->getPricesCountOfOrderedServices($id, $val['galioja_nuo']);
  				if($priceCount > 0) {
  					$val['neaktyvus'] = 1;
  				}
  				$fields['paslaugos_kainos'][] = $val;
  			}
  		}
  	}
  }

  $template->assign('fields', $fields);
  $template->assign('required', $required);
  $template->assign('maxLengths', $maxLengths);


  if (!empty($formErrors))
    $template->assign('formErrors', $formErrors);

  $template->setView("service_edit");
}

else if(!empty($removeId)) {
  // patikriname, ar šalinama paslauga nenaudojama jokioje sutartyje
  $contractCount = $servicesObj->getContractCountOfService($removeId);
  		
  $removeErrorParameter = '';
  if($contractCount == 0) {
  	// pašaliname paslaugos kainas
  	$servicesObj->deleteServicePrices($removeId);
  	
  	// pašaliname paslaugą
  	$servicesObj->deleteService($removeId);
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
  $elementCount = $servicesObj->getServicesListCount();

  // sukuriame puslapiavimo klasės objektą
  $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

  // suformuojame sąrašo puslapius
  $paging->process($elementCount, $pageId);

  // išrenkame nurodyto puslapio sutartis
	$data = $servicesObj->getServicesList($paging->size, $paging->first);
  $template->assign('data', $data);
  $template->assign('pagingData', $paging->data);

  $template->setView("service_list");

  if(isset($_GET['remove_error']))
    $template->assign('remove_error', true);
}

<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';
require_once 'model/services.class.php';

class serviceController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('pavadinimas', 'kaina', 'galioja_nuo');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array (
    'pavadinimas' => 40,
    'aprasymas' => 300
  );

  // nustatome laukų validatorių tipus
  private $validations = array (
    'pavadinimas' => 'anything',
    'aprasymas' => 'anything',
    'kaina' => 'price',
    'galioja_nuo' => 'date'
  );

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = services::getServicesListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = services::getServicesList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    if(!empty($_GET['id_error']))
      $template->assign('id_error', true);

    $template->setView("service_list");
  }

  public function createAction() {
    $data = $this->validateInput();
    // If entered data was valid
    if ($data) {
      // Find max ID in the database
      $latestId = services::getMaxIdOfService();
      // Increment it by one
      $data['id'] = $latestId + 1;

      // Insert row into database
      services::insertService($data);

      // Insert service prices into database
      services::insertServicePrices($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  public function editAction() {
    $id = routing::getId();

    $service = services::getService($id);
    if ($service == false) {
      routing::redirect(routing::getModule(), 'list', 'id_error=1');
      return;
    }

    $service['kaina'] = array();
    $service['galioja_nuo'] = array();
    $service['neaktyvus'] = array();

    $servicePrices = services::getServicePrices($id);
    foreach ($servicePrices as $price) {
      $service['kaina'][] = $price['kaina'];
      $service['galioja_nuo'][] = $price['galioja_nuo'];
      // If the price is used in orders, we shouldn't allow it to be edited
      $service['neaktyvus'][] = $price['naudojama_uzsakymuose'];
    }

    // Fill form fields with current data
    $template = template::getInstance();
    $template->assign('fields', $service);

    $data = $this->validateInput();
    // If Entered data was valid
    if ($data) {
      $data['id'] = $id;

      // Update it in database
      services::updateService($data);

      // Remove prices, following method will remove only unused prices
      services::deleteServicePrices($id);

      // Insert service prices into database
      services::insertServicePrices($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  private function showForm() {
    $template = template::getInstance();
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);
    $template->setView("service_form");
  }

  private function validateInput() {
    // Check if we even have any input
    if (empty($_POST['submit'])) {
      return false;
    }

    // Create Validator object
    $validator = new validator($this->validations,
      $this->required, $this->maxLengths);

    if(!$validator->validate($_POST)) {
      $template = template::getInstance();

      // Overwrite fields array with submitted $_POST values
      $template->assign('fields', $_POST);

      // Get error message
      $formErrors = $validator->getErrorHTML();
      $template->assign('formErrors', $formErrors);
      return false;
    }

    // Prepare data array to be entered into SQL DB
    $data = $validator->preparePostFieldsForSQL();
    return $data;
  }

  public function deleteAction() {
    $id = routing::getId();

    // patikriname, ar šalinama paslauga nenaudojama jokioje sutartyje
    $count = services::getContractCountOfService($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // pašaliname paslaugos kainas
      services::deleteServicePrices($id);

      // pašaliname paslaugą
      services::deleteService($id);
    } else {
      // nepašalinome, nes paslauga naudojama bent vienoje sutartyje
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į paslaugų puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


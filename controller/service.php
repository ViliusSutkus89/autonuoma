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

    // prices array is composed of:
    // list of immutable prices, that are in use by contracts. supplied from the database
    // list of editable prices, supplied either from
    //   * $_POST - if the form was submitted
    //   * database
    $usedPrices = array();
    $unusedPrices = array();

    $id = routing::getId();
    if ($id) {
      $usedPrices = services::getServicePrices($id, 1);
      if (empty($_POST['submit'])) {
        $unusedPrices = services::getServicePrices($id, 0);
      }
    }

    if (!empty($_POST['kaina'])) {
      foreach(array_keys($_POST['kaina']) as $key) {
        $unusedPrices[] = array(
          'kaina' => $_POST['kaina'][$key],
          'galioja_nuo' => $_POST['galioja_nuo'][$key]
        );
      }
    }

    $prices = array_merge(
      array_values($usedPrices),
      array_values($unusedPrices)
    );

    $template->assign('prices', $prices);
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

    // pašaliname paslaugos kainas
    services::deleteServicePrices($id);

    // pašaliname paslaugą
    $err = (services::deleteService($id)) ? '' : 'delete_error=1';

    // Redirect back to the list
    routing::redirect(routing::getModule(), 'list', $err);
  }

};


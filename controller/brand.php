<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/brands.class.php';

class brandController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('pavadinimas');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array ('pavadinimas' => 20);

  // nustatome laukų validatorių tipus
  private $validations = array ('pavadinimas' => 'anything');

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = brands::getBrandListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = brands::getBrandList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    if(!empty($_GET['id_error']))
      $template->assign('id_error', true);

    $template->setView("brand_list");
  }

  public function createAction() {
    $data = $this->validateInput();
    // If entered data was valid
    if ($data) {
      // Find max ID in the database
      $latestId = brands::getMaxIdOfBrand();
      // Increment it by one
      $data['id'] = $latestId + 1;

      // Insert row into database
      brands::insertBrand($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  public function editAction() {
    $id = routing::getId();

    $brand = brands::getBrand($id);
    if ($brand == false) {
      routing::redirect(routing::getModule(), 'list', 'id_error=1');
      return;
    }

    // Fill form fields with current data
    $template = template::getInstance();
    $template->assign('fields', $brand);

    $data = $this->validateInput();
    // If Entered data was valid
    if ($data) {
      $data['id'] = $id;

      // Update it in database
      brands::updateBrand($data);

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
    $template->setView("brand_form");
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

    // šaliname markę
    $err = (brands::deleteBrand($id)) ? '' : 'delete_error=1';

    // nukreipiame į markių puslapį
    routing::redirect(routing::getModule(), 'list', $err);
  }

};


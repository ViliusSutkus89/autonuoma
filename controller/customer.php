<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/customers.class.php';

class customerController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('asmens_kodas', 'vardas', 'pavarde', 'gimimo_data', 'telefonas');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array (
    'asmens_kodas' => 11,
    'vardas' => 20,
    'pavarde' => 20
  );

  // nustatome laukų validatorių tipus
  private $validations = array (
    'asmens_kodas' => 'positivenumber',
    'vardas' => 'alfanum',
    'pavarde' => 'alfanum',
    'gimimo_data' => 'date',
    'telefonas' => 'phone',
    'epastas' => 'email'
  );

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = customers::getCustomersListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = customers::getCustomersList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    if(!empty($_GET['id_error']))
      $template->assign('id_error', true);

    $template->setView("customer_list");
  }

  public function createAction() {
    $data = $this->validateInput();
    // If entered data was valid
    if ($data) {
      // Insert row into database
      customers::insertcustomer($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  public function editAction() {
    $id = routing::getId();

    $customer = customers::getCustomer($id);
    if ($customer == false) {
      routing::redirect(routing::getModule(), 'list', 'id_error=1');
      return;
    }

    // Fill form fields with current data
    $template = template::getInstance();
    $template->assign('fields', $customer);
    $template->assign('editing', true);

    $data = $this->validateInput();
    // If Entered data was valid
    if ($data) {
      $data['asmens_kodas'] = $id;

      // Update it in database
      customers::updateCustomer($data);

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
    $template->setView("customer_form");
  }

  private function validateInput() {
    // Check if we even have any input
    if (empty($_POST['submit'])) {
      return false;
    }

    // Create Validator object
    $validator = new validator($this->validations,
      $this->required, $this->maxLengths);

    if($validator->validate($_POST)) {
      // Prepare data array to be entered into SQL DB
      $data = $validator->preparePostFieldsForSQL();

      // If We're creating a new entry
      // We need to make sure that the ID is unique
      if (routing::getId() || !customers::getCustomer($data['asmens_kodas'])) {
        return $data;
      }
      $formErrors = "Vartotojas su įvestu asmens kodu jau egzistuoja.";
    } else {
      $formErrors = $validator->getErrorHTML();
    }
    $template = template::getInstance();

    // Overwrite fields array with submitted $_POST values
    $template->assign('fields', $_POST);
    $template->assign('formErrors', $formErrors);
    return false;
  }

  public function deleteAction() {
    $id = routing::getId();

    // patikriname, ar klientas neturi sudarytų sutarčių
    $count = customers::getContractCountOfCustomer($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // šaliname klientą
      customers::deleteCustomer($id);
    } else {
      // nepašalinome, nes klientas sudaręs bent vieną sutartį, rodome klaidos pranešimą
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // Redirect back to the list
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


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
      if (customers::insertcustomer($data)) {
        // Redirect back to the list
        routing::redirect(routing::getModule(), 'list');
      } else {
        // Overwrite fields array with submitted $_POST values
        $template = template::getInstance();
        $template->assign('fields', $_POST);
        $template->assign('formErrors', "Duplicate ID!");
        $this->showForm();
      }
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

    if(!$validator->validate($_POST)) {
      // Overwrite fields array with submitted $_POST values
      $template = template::getInstance();
      $template->assign('fields', $_POST);

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

    $err = (customers::deleteCustomer($id)) ? '' : 'delete_error=1';

    // Redirect back to the list
    routing::redirect(routing::getModule(), 'list', $err);
  }

};


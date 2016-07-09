<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/employees.class.php';

class employeeController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('tabelio_nr', 'vardas', 'pavarde');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array (
    'tabelio_nr' => 6,
    'vardas' => 20,
    'pavarde' => 20
  );

  // nustatome laukų validatorių tipus
  private $validations = array (
    'tabelio_nr' => 'alfanum',
    'vardas' => 'alfanum',
    'pavarde' => 'alfanum');

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = employees::getEmployeesListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = employees::getEmployeesList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    if(!empty($_GET['id_error']))
      $template->assign('id_error', true);

    $template->setView("employee_list");
  }

  public function createAction() {
    $data = $this->validateInput();
    // If entered data was valid
    if ($data) {

      // Insert row into database
      if (employees::insertEmployee($data)) {
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

    $employee = employees::getEmployee($id);
    if ($employee == false) {
      routing::redirect(routing::getModule(), 'list', 'id_error=1');
      return;
    }

    // Fill form fields with current data
    $template = template::getInstance();
    $template->assign('fields', $employee);

    $data = $this->validateInput();
    // If Entered data was valid
    if ($data) {
      $data['tabelio_nr'] = $id;

      // Update it in database
      employees::updateEmployee($data);

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
    $template->setView("employee_form");
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
    // šaliname darbuotoją
    $err = (employees::deleteEmployee($id)) ? '' : 'delete_error=1';

    // nukreipiame į darbuotojų puslapį
    routing::redirect(routing::getModule(), 'list', $err);
  }

};


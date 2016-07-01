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
    // sukuriame employeeių klasės objektą
    $employeesObj = new employees();

    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = $employeesObj->getEmployeesListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = $employeesObj->getEmployeesList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    $template->setView("employee_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $employeesObj = new employees();

    $fields = array();
    // tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
    if ($id) {
      $fields = $employeesObj->getEmployee($id);
      $fields['editing'] = 1;
    }

    $template = template::getInstance();

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("employee_edit");
  }

  private function insertUpdateAction() {
    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required, $this->maxLengths);

    // laukai įvesti be klaidų
    if($validator->validate($_POST)) {
      $employeesObj = new employees();

      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();
      if(isset($data['editing'])) {
        // atnaujiname duomenis
        $employeesObj->updateEmployee($data);
      } else {
        // įrašome naują darbuotoją
        $employeesObj->insertEmployee($data);
      }

      // nukreipiame į darbuotojų puslapį
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showAction();

      $template = template::getInstance();

      // Overwrite fields array with submitted $_POST values
      $template->assign('fields', $_POST);

      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();
      $template->assign('formErrors', $formErrors);
    }
  }

  public function deleteAction() {
    $id = routing::getId();

    // patikriname, ar darbuotojas neturi sudarytų sutarčių
    $employeesObj = new employees();
    $count = $employeesObj->getContractCountOfEmployee($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // šaliname darbuotoją
      $employeesObj->deleteEmployee($id);
    } else {
      // nepašalinome, nes klientas sudaręs bent vieną sutartį, rodome klaidos pranešimą
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į darbuotojų puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


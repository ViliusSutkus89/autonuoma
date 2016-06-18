<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/customers.class.php';

class customerController {

  public static $defaultAction = "index";

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

  public function indexAction() {
    // sukuriame customerių klasės objektą
    $customersObj = new customers();

    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = $customersObj->getCustomersListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = $customersObj->getCustomersList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['remove_error']))
      $template->assign('remove_error', true);

    $template->setView("customer_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $customersObj = new customers();

    $fields = array();
		// tikriname, ar nurodytas elemento id. Jeigu taip, išrenkame elemento duomenis ir jais užpildome formos laukus.
    if ($id) {
      $fields = $customersObj->getCustomer($id);
			$fields['editing'] = 1;
    }

    $template = template::getInstance();

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("customer_edit");
  }

  private function insertUpdateAction() {
		// sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required, $this->maxLengths);

		// laukai įvesti be klaidų
		if($validator->validate($_POST)) {
      $customersObj = new customers();

			// suformuojame laukų reikšmių masyvą SQL užklausai
			$data = $validator->preparePostFieldsForSQL();
			if(isset($data['editing'])) {
				// atnaujiname duomenis
				$customersObj->updateCustomer($data);
			} else {
				// įrašome naują klientą
				$customersObj->insertCustomer($data);
			}

			// nukreipiame į customerių puslapį
      routing::redirect(routing::getModule(), 'index');
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

  public function removeAction() {
    $id = routing::getId();

    // patikriname, ar klientas neturi sudarytų sutarčių
    $customersObj = new customers();
    $count = $customersObj->getContractCountOfCustomer($id);

    $removeErrorParameter = '';
    if($count == 0) {
      // šaliname klientą
      $customersObj->deleteCustomer($id);
    } else {
      // nepašalinome, nes klientas sudaręs bent vieną sutartį, rodome klaidos pranešimą
      // rodome klaidos pranešimą
      $removeErrorParameter = 'remove_error=1';
    }

    // nukreipiame į markių puslapį
    routing::redirect(routing::getModule(), 'index',
      $removeErrorParameter);
  }

};


<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/cars.class.php';
require_once 'model/contracts.class.php';
require_once 'model/customers.class.php';
require_once 'model/employees.class.php';
require_once 'model/services.class.php';

class contractController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('nr', 'sutarties_data', 'nuomos_data_laikas', 'planuojama_grazinimo_data_laikas', 'pradine_rida', 'kaina', 'degalu_kiekis_paimant', 'busena', 'fk_klientas', 'fk_darbuotojas', 'fk_automobilis', 'fk_grazinimo_vieta', 'fk_paemimo_vieta', 'kiekiai');

  // nustatome laukų validatorių tipus
  private $validations = array (
    'nr' => 'positivenumber',
    'sutarties_data' => 'date',
    'nuomos_data_laikas' => 'datetime',
    'planuojama_grazinimo_data_laikas' => 'datetime',
    'faktine_grazinimo_data_laikas' => 'datetime',
    'pradine_rida' => 'int',
    'galine_rida' => 'int',
    'kaina' => 'price',
    'degalu_kiekis_paimant' => 'int',
    'dagalu_kiekis_grazinus' => 'int',
    'busena' => 'positivenumber',
    'fk_klientas' => 'alfanum',
    'fk_darbuotojas' => 'alfanum',
    'fk_automobilis' => 'positivenumber',
    'fk_grazinimo_vieta' => 'positivenumber',
    'fk_paemimo_vieta' => 'positivenumber',
    'kiekiai' => 'int'
  );

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = contracts::getContractListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = contracts::getContractList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['id_error']))
      $template->assign('id_error', true);

    $template->setView("contract_list");
  }

  public function createAction() {
    $data = $this->validateInput();
    // If entered data was valid
    if ($data) {
      // Insert row into database
      contracts::insertContract($data);

      // įrašome užsakytas paslaugas
      contracts::updateOrderedServices($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  public function editAction() {
    $id = routing::getId();

    $contract = contracts::getContract($id);
    if ($contract == false) {
      routing::redirect(routing::getModule(), 'list', 'id_error=1');
      return;
    }
    $contract['uzsakytos_paslaugos'] = contracts::getOrderedServices($id);

    $template = template::getInstance();
    $template->assign('fields', $contract);
    $template->assign('editing', true);

    $data = $this->validateInput();
    // If Entered data was valid
    if ($data) {
      $data['nr'] = $id;

      // Update it in database
      contracts::updateContract($data);

      // Update ordered services
      contracts::updateOrderedServices($data);

      // Redirect back to the list
      routing::redirect(routing::getModule(), 'list');
    } else {
      $this->showForm();
    }
  }

  private function showForm() {
    $template = template::getInstance();
    $services = services::getPricedServices();
    $template->assign('services', $services);

    $template->assign('customerList', customers::getCustomersList());
    $template->assign('employeesList', employees::getEmployeesList());
    $template->assign('contractStates', contracts::getContractStates());
    $template->assign('carsList', cars::getCarList());
    $template->assign('parkingLots', contracts::getParkingLots());
    $template->assign('required', $this->required);

    $template->setView("contract_form");
  }

  private function validateInput() {
    // Check if we even have any input
    if (empty($_POST['submit'])) {
      return false;
    }

    // Create Validator object
    $validator = new validator($this->validations, $this->required);
    if($validator->validate($_POST)) {
      // Prepare data array to be entered into SQL DB
      $data = $validator->preparePostFieldsForSQL();

      // If We're creating a new entry
      // We need to make sure that the ID is unique
      if (routing::getId() || !contracts::getContract($data['nr'])) {
        return $data;
      }
      $formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
    } else {
      $formErrors = $validator->getErrorHTML();
    }
    $template = template::getInstance();

    // laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
    $fields = $_POST;
    if(isset($_POST['kiekiai']) && sizeof($_POST['kiekiai']) > 0) {
      $i = 0;
      foreach($_POST['kiekiai'] as $key => $val) {
        $fields['uzsakytos_paslaugos'][$i]['kiekis'] = $val;
        $i++;
      }
    }
    $template->assign('fields', $fields);
    $template->assign('formErrors', $formErrors);
    return false;
  }

  public function deleteAction() {
    $id = routing::getId();

    // pašaliname užsakytas paslaugas
    contracts::deleteOrderedServices($id);

    // šaliname sutartį
    contracts::deleteContract($id);

    // nukreipiame į sutarčių puslapį
    routing::redirect(routing::getModule(), 'list');
  }

};


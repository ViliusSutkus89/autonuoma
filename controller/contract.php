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

    $template->setView("contract_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $fields = array();
    if ($id) {
      $fields = contracts::getContract($id);
      $fields['uzsakytos_paslaugos'] = contracts::getOrderedServices($id);
      $fields['editing'] = 1;
    }

    $template = template::getInstance();

    $template->assign('customerList', customers::getCustomersList());
    $template->assign('employeesList', employees::getEmployeesList());
    $template->assign('contractStates', contracts::getContractStates());
    $template->assign('carsList', cars::getCarList());
    $template->assign('parkingLots', contracts::getParkingLots());

    $servicesList = sevices::getServicesList();

    $serviceIDs = array();
    foreach($servicesList as $val)
      $serviceIDs[] = $val['id'];

    $servicePrices = sevices::getServicePrices($serviceIDs);

    $template->assign('servicesList', $servicesList);
    $template->assign('servicePrices', $servicePrices);

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);

    $template->setView("contract_edit");
  }

  private function insertUpdateAction() {

    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required);

    // laukai įvesti be klaidų
    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();

      if(isset($data['editing'])) {
        // atnaujiname sutartį
        contracts::updateContract($data);
        // atnaujiname užsakytas paslaugas
        contracts::updateOrderedServices($data);
      } else {
        // patikriname, ar nėra sutarčių su tokiu pačiu numeriu
        $exists = contracts::getContract($data['nr']);
        if($exists) {
          // sudarome klaidų pranešimą
          $formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
          // laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
          $fields = $_POST;

          $this->showAction();

          $template = template::getInstance();
          $template->assign('fields', $fields);
          $template->assign('formErrors', $formErrors);
        } else {
          // įrašome naują sutartį
          contracts::insertContract($data);
          // įrašome užsakytas paslaugas
          contracts::updateOrderedServices($data);
        }
      }

    } else {
      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();

      $this->showAction();

      $template = template::getInstance();

      $template->assign('formErrors', $formErrors);

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
    }

    if (empty($formErrors)) {
      // nukreipiame vartotoją į sutarčių puslapį
      routing::redirect(routing::getModule(), 'list');
    }
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


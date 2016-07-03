<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/cars.class.php';
require_once 'model/brands.class.php';
require_once 'model/models.class.php';


class carController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('modelis', 'valstybinis_nr', 'pagaminimo_data', 'pavaru_deze', 'degalu_tipas', 'kebulas', 'bagazo_dydis', 'busena', 'rida', 'vietu_skaicius', 'registravimo_data', 'verte');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array (
    'valstybinis_nr' => 6
  );

  // nustatome laukų validatorių tipus
  private $validations = array (
    'modelis' => 'positivenumber',
    'valstybinis_nr' => 'alfanum',
    'pavaru_deze' => 'positivenumber',
    'degalu_tipas' => 'positivenumber',
    'kebulas' => 'positivenumber',
    'bagazo_dydis' => 'positivenumber',
    'busena' => 'positivenumber',
    'pagaminimo_data' => 'date',
    'rida' => 'positivenumber',
    'vietu_skaicius' => 'positivenumber',
    'registravimo_data' => 'date',
    'verte' => 'price'
  );

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = cars::getCarListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = cars::getCarList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    $template->setView("car_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $fields = ($id) ? cars::getcar($id) : array();

    $template = template::getInstance();

    $brands = brands::getBrandList();
    $brandIDs = array();
    foreach($brands as $val)
      $brandIDs[] = $val['id'];
    $models = models::getModelsListByBrands($brandIDs);

    $template->assign('brands', $brands);
    $template->assign('models', $models);

    $gearboxes = cars::getGearboxList();
    $fueltypes = cars::getFuelTypeList();
    $bodytypes = cars::getBodyTypeList();
    $luggage = cars::getLuggageTypeList();
    $car_states = cars::getCarStateList();

    $template->assign('gearboxes', $gearboxes);
    $template->assign('fueltypes', $fueltypes);
    $template->assign('bodytypes', $bodytypes);
    $template->assign('luggage', $luggage);

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("car_edit");
  }

  private function insertUpdateAction() {
    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required, $this->maxLengths);

    // laukai įvesti be klaidų
    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();

      // sutvarkome checkbox reikšmes
      $data['radijas'] = (!empty($data['radijas']) && $data['radijas'] == 'on') ? 1 : 0;

      $data['grotuvas'] = (!empty($data['grotuvas']) && $data['grotuvas'] == 'on') ? 1 : 0;

      $data['kondicionierius'] =  (!empty($data['kondicionierius']) && $data['kondicionierius'] == 'on') ? 1 : 0;

      if(isset($data['id'])) {
        // atnaujiname duomenis
        cars::updateCar($data);
      } else {
        // randame didžiausią markės id duomenų bazėje
        $latestId = cars::getMaxIdOfcar();

        // įrašome naują įrašą
        $data['id'] = $latestId + 1;
        cars::insertCar($data);
      }

      // nukreipiame į automobilių puslapį
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

    // patikriname, ar automobilis neįtrauktas į sutartis
    $count = cars::getContractCountOfCar($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // pašaliname automobilį
      cars::deleteCar($id);
    } else {
      // nepašalinome, nes automobilis įtrauktas bent į vieną sutartį, rodome klaidos pranešimą
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į markių puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/models.class.php';
require_once 'model/brands.class.php';


class modelController {

  public static $defaultAction = "list";

  // nustatome privalomus laukus
  private $required = array('pavadinimas', 'fk_marke');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array ('pavadinimas' => 20);

  // nustatome laukų validatorių tipus
  private $validations = array (
    'pavadinimas' => 'anything',
    'fk_marke' => 'positivenumber'
  );

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = models::getModelListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = models::getModelList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);

    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

    $template->setView("model_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $fields = ($id) ? models::getmodel($id) : array();

    $template = template::getInstance();

    $brands = brands::getBrandList();
    $template->assign('brands', $brands);

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("model_edit");
  }

  private function insertUpdateAction() {
    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required, $this->maxLengths);

    // laukai įvesti be klaidų
    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();
      if(isset($data['id'])) {
        // atnaujiname duomenis
        models::updateModel($data);
      } else {
        // randame didžiausią markės id duomenų bazėje
        $latestId = models::getMaxIdOfmodel();

        // įrašome naują įrašą
        $data['id'] = $latestId + 1;
        models::insertModel($data);
      }

      // nukreipiame į modelių puslapį
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

    // patikriname, ar šalinamas modelis nenaudojamas, t.y. nepriskirtas jokiam automobiliui
    $count = models::getCarCountOfModel($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // pašaliname modelį
      models::deleteModel($id);
    } else {
      // nepašalinome, nes modelis priskirtas bent vienam automobiliui, rodome klaidos pranešimą
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į markių puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


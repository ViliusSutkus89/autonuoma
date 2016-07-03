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

    $template->setView("brand_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $fields = ($id) ? brands::getBrand($id) : array();

    $template = template::getInstance();

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("brand_edit");
  }

  private function insertUpdateAction() {
    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations,
      $this->required, $this->maxLengths);

    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();
      if(isset($data['id'])) {
        // atnaujiname duomenis
        brands::updateBrand($data);
      } else {
        // randame didžiausią markės id duomenų bazėje
        $latestId = brands::getMaxIdOfBrand();

        // įrašome naują įrašą
        $data['id'] = $latestId + 1;
        brands::insertBrand($data);
      }

      // nukreipiame į markių puslapį
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

    // patikriname, ar šalinama markė nepriskirta modeliui
    $count = brands::getModelCountOfBrand($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // šaliname markę
      brands::deleteBrand($id);
    } else {
      // nepašalinome, nes markė priskirta modeliui,
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į markių puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


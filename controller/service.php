<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';
require_once 'model/services.class.php';

class serviceController {

  public static $defaultAction = "index";

  // nustatome privalomus laukus
  private $required = array('pavadinimas', 'kainos', 'datos');

  // maksimalūs leidžiami laukų ilgiai
  private $maxLengths = array (
    'pavadinimas' => 40,
    'aprasymas' => 300
  );

  // nustatome laukų validatorių tipus
  private $validations = array (
    'pavadinimas' => 'anything',
    'aprasymas' => 'anything',
    'kainos' => 'price',
    'datos' => 'date'
  );

  public function indexAction() {
    // sukuriame markių klasės objektą
    $servicesObj = new services();

    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = $servicesObj->getServicesListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = $servicesObj->getServicesList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);
    if(!empty($_GET['remove_error']))
      $template->assign('remove_error', true);

    $template->setView("service_list");
  }

  public function editAction() {
    if (!empty($_POST['submit']))
      $this->insertUpdateAction();
    else
      $this->showAction();
  }

  private function showAction() {
    $id = routing::getId();

    $servicesObj = new services();
    $fields = array();
    if ($id) {
      $fields = $servicesObj->getService($id);
      $tmp = $servicesObj->getServicePrices($id);
      if(sizeof($tmp) > 0) {
        $contractsObj = new contracts();
        foreach($tmp as $key => $val) {
          // jeigu paslaugos kaina yra naudojama, jos koreguoti neleidziame ir įvedimo laukelį padarome neaktyvų
          $priceCount = $contractsObj->getPricesCountOfOrderedServices($id, $val['galioja_nuo']);
          if($priceCount > 0) {
            $val['neaktyvus'] = 1;
          }
          $fields['paslaugos_kainos'][] = $val;
        }
      }
    }

    $template = template::getInstance();

    $template->assign('fields', $fields);
    $template->assign('required', $this->required);
    $template->assign('maxLengths', $this->maxLengths);

    $template->setView("service_edit");
  }

  private function insertUpdateAction() {
    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations, $this->required, $this->maxLengths);

    // laukai įvesti be klaidų
    if($validator->validate($_POST)) {
      $servicesObj = new services();

      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();
      if(isset($data['id'])) {
        // atnaujiname duomenis
        $servicesObj->updateService($data);

        // pašaliname paslaugos kainas, kurios nėra naudojamos sutartyse
        $galiojaNuo = array();
        foreach($data['kainos'] as $key=>$val) {
          if($data['neaktyvus'][$key] == 1) {
            $galiojaNuo[] = $data['datos'][$key];

          }
        }
        $servicesObj->deleteServicePrices($data['id'], $galiojaNuo);

        // atnaujiname paslaugos kainas, kurios nėra naudojamos sutartyse
        $servicesObj->insertServicePrices($data);
      } else {
        // randame didžiausią markės id duomenų bazėje
        $latestId = $servicesObj->getMaxIdOfService();

        // įrašome naują įrašą
        $data['id'] = $latestId + 1;
        $servicesObj->insertService($data);

        // įrašome paslaugų kainas
        $servicesObj->insertServicePrices($data);
      }

      // nukreipiame į paslaugų puslapį
      routing::redirect(routing::getModule(), 'index');
    } else {
      $this->showAction();

      $template = template::getInstance();

      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();
      $template->assign('formErrors', $formErrors);

      $fields = $_POST;
      if(isset($_POST['kainos']) && sizeof($_POST['kainos']) > 0) {
        $i = 0;
        foreach($_POST['kainos'] as $key => $val) {
          $fields['paslaugos_kainos'][$i]['kaina'] = $val;
          $fields['paslaugos_kainos'][$i]['galioja_nuo'] = $_POST['datos'][$key];
          $fields['paslaugos_kainos'][$i]['neaktyvus'] = $_POST['neaktyvus'][$key];
          $i++;
        }
      }
      $template->assign('fields', $fields);
    }
  }

  public function removeAction() {
    $id = routing::getId();

    $servicesObj = new services();
    // patikriname, ar šalinama paslauga nenaudojama jokioje sutartyje
    $count = $servicesObj->getContractCountOfService($id);

    $removeErrorParameter = '';
    if($count == 0) {
      // pašaliname paslaugos kainas
      $servicesObj->deleteServicePrices($id);

      // pašaliname paslaugą
      $servicesObj->deleteService($id);
    } else {
      // nepašalinome, nes paslauga naudojama bent vienoje sutartyje
      // rodome klaidos pranešimą
      $removeErrorParameter = 'remove_error=1';
    }

    // nukreipiame į paslaugų puslapį
    routing::redirect(routing::getModule(), 'index',
      $removeErrorParameter);
  }

};


<?php
require_once 'utils/paging.class.php';
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';
require_once 'model/services.class.php';

class serviceController {

  public static $defaultAction = "list";

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

  public function listAction() {
    // suskaičiuojame bendrą įrašų kiekį
    $elementCount = services::getServicesListCount();

    // sukuriame puslapiavimo klasės objektą
    $paging = new paging(NUMBER_OF_ROWS_IN_PAGE);

    // suformuojame sąrašo puslapius
    $paging->process($elementCount, routing::getPageId());

    // išrenkame nurodyto puslapio markes
    $data = services::getServicesList($paging->size, $paging->first);

    $template = template::getInstance();

    $template->assign('data', $data);
    $template->assign('pagingData', $paging->data);
    if(!empty($_GET['delete_error']))
      $template->assign('delete_error', true);

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

    $fields = array();
    if ($id) {
      $fields = services::getService($id);
      // Check if this service is actually found in the db
      if ($fields) {
        $servicePrices = services::getServicePrices($id);
        //getServicePrices return an array of prices from multiple services, we only need one
        if (!empty($servicePrices)) {
          $servicePrices = $servicePrices[$id];

          $galioja_nuo = array();

          foreach($servicePrices as $val) {
            $galioja_nuo[] = $val['galioja_nuo'];
          }

          $priceCounts = contracts::getPricesCountOfOrderedServices($id, $galioja_nuo);
          foreach($servicePrices as $val) {
            // jeigu paslaugos kaina yra naudojama, jos koreguoti neleidziame ir įvedimo laukelį padarome neaktyvų
            if (!empty($priceCounts[$val['galioja_nuo']])) {
              $val['neaktyvus'] = 1;
            }
            $fields['paslaugos_kainos'][] = $val;
          }
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
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();
      if(isset($data['id'])) {
        // atnaujiname duomenis
        services::updateService($data);

        // pašaliname paslaugos kainas, kurios nėra naudojamos sutartyse
        $galiojaNuo = array();
        if (!empty($data['kainos'])) {
          foreach($data['kainos'] as $key=>$val) {
            if($data['neaktyvus'][$key] == 1) {
              $galiojaNuo[] = $data['datos'][$key];

            }
          }
        }
        services::deleteServicePrices($data['id'], $galiojaNuo);

        // atnaujiname paslaugos kainas, kurios nėra naudojamos sutartyse
        services::insertServicePrices($data);
      } else {
        // randame didžiausią markės id duomenų bazėje
        $latestId = services::getMaxIdOfService();

        // įrašome naują įrašą
        $data['id'] = $latestId + 1;
        services::insertService($data);

        // įrašome paslaugų kainas
        services::insertServicePrices($data);
      }

      // nukreipiame į paslaugų puslapį
      routing::redirect(routing::getModule(), 'list');
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

  public function deleteAction() {
    $id = routing::getId();

    // patikriname, ar šalinama paslauga nenaudojama jokioje sutartyje
    $count = services::getContractCountOfService($id);

    $deleteErrorParameter = '';
    if($count == 0) {
      // pašaliname paslaugos kainas
      services::deleteServicePrices($id);

      // pašaliname paslaugą
      services::deleteService($id);
    } else {
      // nepašalinome, nes paslauga naudojama bent vienoje sutartyje
      // rodome klaidos pranešimą
      $deleteErrorParameter = 'delete_error=1';
    }

    // nukreipiame į paslaugų puslapį
    routing::redirect(routing::getModule(), 'list',
      $deleteErrorParameter);
  }

};


<?php
require_once 'utils/template.class.php';
require_once 'utils/validator.class.php';
require_once 'model/contracts.class.php';
require_once 'model/services.class.php';

class reportController {
  public static $defaultAction = "index";

  // List all the available reports
  private static $reports = array(
    1 => array(
      "title" => "Sutarčių ataskaita",
      "description" => "Per laikotarpį sudarytų sutarčių ataskaita.",
      "controllerName" => "contract_report"
    ),

    2 => array(
      "title" => "Užsakytų paslaugų ataskaita",
      "description" => "Per laikotarpį užsakytų papildomų paslaugų ataskaita.",
      "controllerName" => "service_report"
    ),

    3 => array(
      "title" => "Vėluojamų grąžinti automobilių ataskaita",
      "description" => "Negrąžintų arba pavėluotai grąžintų automobilių ataskaita.",
      "controllerName" => "delayed_cars_report"
    )
  );

  public function indexAction() {
    $template = template::getInstance();
    $template->setView("reportIndex");

    $template->assign("reports", self::$reports);
  }

  public function viewAction() {

    // Find out which report are we working with
    $id = routing::getId();
    if (!empty(self::$reports[$id])) {
      $reportController = self::$reports[$id]["controllerName"];

      $rC = new $reportController();
      if (!empty($_POST['submit'])) {
        $rC->showResult();
      } else {
        $rC->showForm();
      }
    } else {
      //error, report not found
      die("Report {$id} not found!");
    }
  }

};


class contract_report {

  // nustatome laukų validatorių tipus
  private  $validations = array (
    'dataNuo' => 'date',
    'dataIki' => 'date'
  );

  public function showForm() {
    template::getInstance()->setView("contract_report_form");
  }

  public function showResult() {
    $template = template::getInstance();

    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations);

    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();

      // išrenkame ataskaitos duomenis
      $contractData = contracts::getCustomerContracts($data['dataNuo'], $data['dataIki']);
      $totalPrice = contracts::getSumPriceOfContracts($data['dataNuo'], $data['dataIki']);
      $totalServicePrice = contracts::getSumPriceOfOrderedServices($data['dataNuo'], $data['dataIki']);

      $template->assign("data", $data);
      $template->assign("contractData", $contractData);
      $template->assign("totalPrice", $totalPrice);
      $template->assign("totalServicePrice", $totalServicePrice);

      $template->setView("contract_report");
    } else {

      $this->showForm();

      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();

      // gauname įvestus laukus
      $fields = $_POST;

      $template->assign("formErrors", $formErrors);
      $template->assign("fields", $fields);
    }
  }
};


class service_report {

  // nustatome laukų validatorių tipus
  private  $validations = array (
    'dataNuo' => 'date',
    'dataIki' => 'date'
  );

  public function showForm() {
    template::getInstance()->setView("service_report_form");
  }

  public function showResult() {
    $template = template::getInstance();

    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations);

    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();

      // išrenkame ataskaitos duomenis
      $servicesData = services::getOrderedServices($data['dataNuo'], $data['dataIki']);
      $servicesStats = services::getStatsOfOrderedServices($data['dataNuo'], $data['dataIki']);

      $template->assign("servicesData", $servicesData);
      $template->assign("servicesStats", $servicesStats);
      $template->assign("data", $data);

      $template->setView("service_report");
    } else {

      $this->showForm();

      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();

      // gauname įvestus laukus
      $fields = $_POST;

      $template->assign("formErrors", $formErrors);
      $template->assign("fields", $fields);
    }
  }
};


class delayed_cars_report {

  // nustatome laukų validatorių tipus
  private  $validations = array (
    'dataNuo' => 'date',
    'dataIki' => 'date'
  );

  public function showForm() {
    template::getInstance()->setView("delayed_cars_report_form");
  }

  public function showResult() {
    $template = template::getInstance();

    // sukuriame validatoriaus objektą
    $validator = new validator($this->validations);

    if($validator->validate($_POST)) {
      // suformuojame laukų reikšmių masyvą SQL užklausai
      $data = $validator->preparePostFieldsForSQL();

      // išrenkame ataskaitos duomenis
      $delayedCarsData = contracts::getDelayedCars($data['dataNuo'], $data['dataIki']);

      $template->assign("delayedCarsData", $delayedCarsData);
      $template->assign("data", $data);

      $template->setView("delayed_cars_report");
    } else {

      $this->showForm();

      // gauname klaidų pranešimą
      $formErrors = $validator->getErrorHTML();

      // gauname įvestus laukus
      $fields = $_POST;

      $template->assign("formErrors", $formErrors);
      $template->assign("fields", $fields);
    }
  }
};


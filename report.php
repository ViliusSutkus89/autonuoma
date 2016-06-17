<?php
// nuskaitome konfigūracijų failą
require_once 'config.php';

// iškviečiame prisijungimo prie duomenų bazės klasę
require_once 'utils/mysql_pdo.class.php';

require_once 'utils/template.class.php';

// nustatome pasirinktos ataskaitos id
// default to id=1
$id = (!empty($_GET['id'])) ? (int) $_GET['id'] : 1;

$template = new template();
$template->assign("id", $id);

switch($id) {
  case 1: $report_module = "contract_report"; break;
  case 2: $report_module = "service_report"; break;
  case 3: $report_module = "delayed_cars_report"; break;
}

if (!empty($report_module))
  require("controller/{$report_module}.php");

$template->render();


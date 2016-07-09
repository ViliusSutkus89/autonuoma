<?php
session_start();

// nuskaitome konfigūracijų failą
require_once 'config.php';

// iškviečiame prisijungimo prie duomenų bazės klasę
require_once 'utils/mysql_pdo.class.php';

require_once 'utils/routing.class.php';
require_once 'utils/controller.class.php';
require_once 'utils/template.class.php';

$controller = new controller();

$template = template::getInstance();
$template->assign('module', routing::getModule());
$template->assign('id', routing::getId());
$template->render();


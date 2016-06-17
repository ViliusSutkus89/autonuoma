<?php
	// nuskaitome konfigūracijų failą
	require_once 'config.php';

	// iškviečiame prisijungimo prie duomenų bazės klasę
	require_once 'utils/mysql_pdo.class.php';

  require_once 'utils/template.class.php';
	
	// nustatome pasirinktą modulį
	$module = '';
	if(isset($_GET['module'])) {
    // Module name can only be a-Z0-9._-
    $module = preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $_GET['module']);
	}
	
	// jeigu pasirinktas elementas (sutartis, automobilis ir kt.), nustatome elemento id
  $id = (!empty($_GET['id'])) ? $_GET['id'] : '';
	
	// nustatome, ar kuriamas naujas elementas
	$action = (!empty($_GET['action'])) ? $_GET['action'] : '';
	
	// jeigu šalinamas elementas, nustatome šalinamo elemento id
	$removeId = (!empty($_GET['remove'])) ? $_GET['remove'] : 0;
		
	// nustatome elementų sąrašo puslapio numerį
	$pageId = (!empty($_GET['page'])) ? $_GET['page'] : 1;
	
  $template = new template();
  $template->assign('module', $module);
  $template->assign('id', $id);

	if(!empty($module)) {
    require "controller/${module}.php";
	} else
    $template->setView("index");

  $template->render();

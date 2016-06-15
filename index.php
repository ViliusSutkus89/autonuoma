<?php
	// nuskaitome konfigūracijų failą
	require 'config.php';

	// iškviečiame prisijungimo prie duomenų bazės klasę
	require 'utils/mysql.class.php';

  require 'utils/template.class.php';
	
	// nustatome pasirinktą modulį
	$module = '';
	if(isset($_GET['module'])) {
    // Module name can only be a-Z0-9._-
    $module = preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $_GET['module']);
	}
	
	// jeigu pasirinktas elementas (sutartis, automobilis ir kt.), nustatome elemento id
	$id = '';
	if(isset($_GET['id'])) {
		$id = mysql::escape($_GET['id']);
	}
	
	// nustatome, ar kuriamas naujas elementas
	$action = '';
	if(isset($_GET['action'])) {
		$action = mysql::escape($_GET['action']);
	}
	
	// jeigu šalinamas elementas, nustatome šalinamo elemento id
	$removeId = 0;
	if(!empty($_GET['remove'])) {
		// paruošiame $_GET masyvo id reikšmę SQL užklausai
		$removeId = mysql::escape($_GET['remove']);
	}
		
	// nustatome elementų sąrašo puslapio numerį
	$pageId = 1;
	if(!empty($_GET['page'])) {
		$pageId = mysql::escape($_GET['page']);
	}
	
  $template = new template();
  $template->assign('module', $module);
  $template->assign('id', $id);

	if(!empty($module)) {
    require "controller/${module}.php";
	} else
    $template->setView("index");

  $template->render();

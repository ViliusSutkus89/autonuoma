<?php

class routing {

  private static $instance = null;
  private $data = array();

  public static function getInstance() {
    if (self::$instance === null)
      self::$instance = new routing();

    return self::$instance;
  }

  public function __construct() {
	  // nustatome pasirinktą modulį
    // Module name can only be a-Z0-9._-
    $module = (!empty($_GET['module'])) ?
      preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $_GET['module']) : DEFAULT_CONTROLLER;
    $this->data['module'] = $module;

	  // jeigu pasirinktas elementas (sutartis, automobilis ir kt.), nustatome elemento id
    $this->data['id'] = (!empty($_GET['id'])) ? $_GET['id'] : '';
	  
	  // nustatome, ar kuriamas naujas elementas
    // action = [list, create, edit, delete]
	  $this->data['action'] = (!empty($_GET['action'])) ? $_GET['action'] : '';
	  
	  // nustatome elementų sąrašo puslapio numerį
    if (!empty($_GET['page']))
      $this->data['pageId'] = $_GET['page'];
    else if (!empty($_SESSION['lastUsedPages'][$module]))
      $this->data['pageId'] = $_SESSION['lastUsedPages'][$module];
    else
      $this->data['pageId'] = 1;

    $_SESSION['lastUsedPages'][$module] = $this->data['pageId'];
  }

  public static function getModule() {
    return self::getInstance()->data['module'];
  }

  public static function getAction() {
    return self::getInstance()->data['action'];
  }

  public static function getId() {
    return self::getInstance()->data['id'];
  }

  public static function getPageId() {
    return self::getInstance()->data['pageId'];
  }

  public static function getURL($controller = '', $action = '', $params = '') {
    $url = 'index.php';
    if (!empty($controller)) {
      $url .= "?module=${controller}";

      if (!empty($action))
        $url .= "&action=" . $action;

      if (!empty($params))
        $url .= "&" . $params;
    }

    return $url;
  }

  public static function redirect($controller, $action = '', $params = '') {
    $url = self::getURL($controller, $action, $params);
    header("Location: ${url}");
  }
};


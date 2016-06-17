<?php

class controller {

  public function __construct() {
    // Select and launch the correct controller and action
    $module = routing::getModule();
    $action = routing::getAction();

    // Require controller class
    require_once "controller/${module}.php";

    $controllerName = $module . 'Controller';
    if (empty($action))
      $action = $controllerName::$defaultAction;

    $actionName = $action . 'Action';

    $controller = new $controllerName();
    $controller->$actionName();
  }

};


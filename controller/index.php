<?php
require_once 'utils/template.class.php';

class indexController {
  public static $defaultAction = "index";

  public function indexAction() {
    template::getInstance()->setView('index');
  }
}


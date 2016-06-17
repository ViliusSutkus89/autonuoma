<?php

/**
 * Puslapio šablono panaudojimo klasė
 *
 * @author V.Sutkus, <v.sutkus@ktu.edu>, IF-4/2
 */

class template {

  private static $instance = null;

  private $enabled = true;
  private $data = array();
  private $view;

  public static function getInstance() {
    if (self::$instance === null)
      self::$instance = new template();

    return self::$instance;
  }

  public function setView($view) {
    $this->view = $view;
  }

  public function render() {
    if (!$this->enabled || empty($this->view))
      return;

    extract($this->data);

    require('view/' . $this->view . '.php');
  }

  public function assign($name, $variable) {
    $this->data[$name] = $variable;
  }

  public function disableRendering() {
    $this->enabled = false;
  }
};

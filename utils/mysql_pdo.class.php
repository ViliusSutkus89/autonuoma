<?php
/**
 * PDO MySQL singleton class
 */

class mysql extends PDO {
  private static $instance = null;

  public function __construct() {
    
    $dsn =
      "mysql:host=" . config::DB_SERVER . ";"
      ."dbname=" . config::DB_NAME . ";"
      . "charset=utf8";

    $options  = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
      parent::__construct($dsn, config::DB_USERNAME,
        config::DB_PASSWORD, $options);
    } catch(PROException $e) {
      die("Could not connect to the db!\n");
    }
  }

  public static function getInstance() {
    if (self::$instance === null)
      self::$instance = new mysql();

    return self::$instance;
  }

}


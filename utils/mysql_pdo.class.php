<?php
/**
 * PDO MySQL singleton class
 */

class mysql extends PDO {
  private static $instance = null;

  public function __construct() {
    $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", DB_SERVER, DB_NAME);
    $options  = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
      parent::__construct($dsn, DB_USERNAME, DB_PASSWORD, $options);
    } catch(PDOException $e) {
      die("Could not connect to the database!\n");
    }
  }

  public static function getInstance() {
    if (self::$instance === null)
      self::$instance = new mysql();

    return self::$instance;
  }

}


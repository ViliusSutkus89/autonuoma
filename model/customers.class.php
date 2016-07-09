<?php

/**
 * Klientų redagavimo klasė
 *
 * @author ISK
 */

class customers {

  /**
   * Kliento išrinkimas
   * @param type $id
   * @return type
   */
  public static function getCustomer($id) {
    $query = "SELECT * FROM `klientai` WHERE `asmens_kodas`= ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();

    if (count($data) == 0) {
      return false;
    }

    return $data[0];
  }

  /**
   * Klientų sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getCustomersList($limit = null, $offset = null) {
    $query = "SELECT * FROM `klientai`";
    $parameters = array();

    if(isset($limit)) {
      $query .= " LIMIT ?";
      $parameters[] = $limit;
    }
    if(isset($offset)) {
      $query .= " OFFSET ?";
      $parameters[] = $offset;
    }

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Klientų kiekio radimas
   * @return type
   */
  public static function getCustomersListCount() {
    $query = "SELECT COUNT(`asmens_kodas`) AS `kiekis` FROM `klientai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Kliento šalinimas
   * @param type $id
   */
  public static function deleteCustomer($id) {
    $query = "DELETE FROM `klientai` WHERE `asmens_kodas`= ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Kliento atnaujinimas
   * @param type $data
   */
  public static function updateCustomer($data) {
    $query = "UPDATE `klientai` SET
        `vardas`= ?,
        `pavarde`= ?,
        `gimimo_data`= ?,
        `telefonas`= ?,
        `epastas`= ?
      WHERE `asmens_kodas`= ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['vardas'],
      $data['pavarde'],
      $data['gimimo_data'],
      $data['telefonas'],
      $data['epastas'],
      $data['asmens_kodas']
    ));
  }

  /**
   * Kliento įrašymas
   * @param type $data
   */
  public static function insertCustomer($data) {
    $query = "INSERT INTO `klientai`
      (
        `asmens_kodas`,
        `vardas`,
        `pavarde`,
        `gimimo_data`,
        `telefonas`,
        `epastas`
      ) 
      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['asmens_kodas'],
      $data['vardas'],
      $data['pavarde'],
      $data['gimimo_data'],
      $data['telefonas'],
      $data['epastas']
    ));
  }

}


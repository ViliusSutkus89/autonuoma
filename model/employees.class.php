<?php

/**
 * Darbuotojų redagavimo klasė
 *
 * @author ISK
 */

class employees {

  /**
   * Darbuotojo išrinkimas
   * @param type $id
   * @return type
   */
  public static function getEmployee($id) {
    $query = "SELECT * FROM `" . DB_PREFIX . "darbuotojai` WHERE `tabelio_nr` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();

    if (count($data) == 0) {
      return false;
    }

    return $data[0];
  }

  /**
   * Darbuotojų sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getEmployeesList($limit = null, $offset = null) {
    $query = "SELECT * FROM `" . DB_PREFIX . "darbuotojai`";
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
   * Darbuotojų kiekio radimas
   * @return type
   */
  public static function getEmployeesListCount() {
    $query = "SELECT COUNT(`tabelio_nr`) AS `kiekis` FROM `" . DB_PREFIX . "darbuotojai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Darbuotojo šalinimas
   * @param type $id
   */
  public static function deleteEmployee($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "darbuotojai` WHERE `tabelio_nr` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Darbuotojo atnaujinimas
   * @param type $data
   */
  public static function updateEmployee($data) {
    $query = "UPDATE `" . DB_PREFIX . "darbuotojai` SET `vardas` = ?, `pavarde` = ? WHERE `tabelio_nr` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['vardas'], $data['pavarde'], $data['tabelio_nr']
    ));
  }

  /**
   * Darbuotojo įrašymas
   * @param type $data
   */
  public static function insertEmployee($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "darbuotojai`
      (`tabelio_nr`, `vardas`, `pavarde`) VALUES ( ?, ?, ? )";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array(
      $data['tabelio_nr'], $data['vardas'], $data['pavarde']
    ));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

}


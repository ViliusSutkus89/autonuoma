<?php

/**
 * Automobilių redagavimo klasė
 *
 * @author ISK
 */

class cars {

  /**
   * Automobilio išrinkimas
   * @param type $id
   * @return type
   */
  public static function getCar($id) {
    $query = "SELECT
        `id`,
        `valstybinis_nr`,
        `pagaminimo_data`,
        `rida`,
        `radijas`,
        `grotuvas`,
        `kondicionierius`,
        `vietu_skaicius`,
        `registravimo_data`,
        `verte`,
        `pavaru_deze`,
        `degalu_tipas`,
        `kebulas`,
        `bagazo_dydis`,
        `busena`,
        `fk_modelis` AS `modelis`
      FROM `" . DB_PREFIX . "automobiliai`
      WHERE `id` = ?";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();

    if (count($data) == 0) {
      return false;
    }

    return $data[0];
  }

  /**
   * Automobilio atnaujinimas
   * @param type $data
   */
  public static function updateCar($data) {
    $query = "
    UPDATE `" . DB_PREFIX . "automobiliai`
    SET
      `valstybinis_nr` = ?,
      `pagaminimo_data` = ?,
      `rida` = ?,
      `radijas` = ?,
      `grotuvas` = ?,
      `kondicionierius` = ?,
      `vietu_skaicius` = ?,
      `registravimo_data` = ?,
      `verte` = ?,
      `pavaru_deze` = ?,
      `degalu_tipas` = ?,
      `kebulas` = ?,
      `bagazo_dydis` = ?,
      `busena` = ?,
      `fk_modelis` = ?
    WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['valstybinis_nr'],
      $data['pagaminimo_data'],
      $data['rida'],
      $data['radijas'],
      $data['grotuvas'],
      $data['kondicionierius'],
      $data['vietu_skaicius'],
      $data['registravimo_data'],
      $data['verte'],
      $data['pavaru_deze'],
      $data['degalu_tipas'],
      $data['kebulas'],
      $data['bagazo_dydis'],
      $data['busena'],
      $data['modelis'],
      $data['id']
    ));
  }

  /**
   * Automobilio įrašymas
   * @param type $data
   */
  public static function insertCar($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "automobiliai` (
        `id`,
        `valstybinis_nr`,
        `pagaminimo_data`,
        `rida`,
        `radijas`,
        `grotuvas`,
        `kondicionierius`,
        `vietu_skaicius`,
        `registravimo_data`,
        `verte`,
        `pavaru_deze`,
        `degalu_tipas`,
        `kebulas`,
        `bagazo_dydis`,
        `busena`,
        `fk_modelis`
      ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['id'],
      $data['valstybinis_nr'],
      $data['pagaminimo_data'],
      $data['rida'],
      $data['radijas'],
      $data['grotuvas'],
      $data['kondicionierius'],
      $data['vietu_skaicius'],
      $data['registravimo_data'],
      $data['verte'],
      $data['pavaru_deze'],
      $data['degalu_tipas'],
      $data['kebulas'],
      $data['bagazo_dydis'],
      $data['busena'],
      $data['modelis']
    ));
  }

  /**
   * Automobilių sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getCarList($limit = null, $offset = null) {
    $query = "SELECT
        `" . DB_PREFIX . "automobiliai`.`id`,
        `" . DB_PREFIX . "automobiliai`.`valstybinis_nr`,
        `" . DB_PREFIX . "auto_busenos`.`name` AS `busena`,
        `" . DB_PREFIX . "modeliai`.`pavadinimas` AS `modelis`,
        `" . DB_PREFIX . "markes`.`pavadinimas` AS `marke`
      FROM
        `" . DB_PREFIX . "automobiliai`
      LEFT JOIN `" . DB_PREFIX . "modeliai`
        ON `" . DB_PREFIX . "automobiliai`.`fk_modelis` = `" . DB_PREFIX . "modeliai`.`id`
      LEFT JOIN `" . DB_PREFIX . "markes`
        ON `" . DB_PREFIX . "modeliai`.`fk_marke` = `" . DB_PREFIX . "markes`.`id`
      LEFT JOIN `" . DB_PREFIX . "auto_busenos`
        ON `" . DB_PREFIX . "automobiliai`.`busena` = `" . DB_PREFIX . "auto_busenos`.`id`";
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
    return $stmt->fetchAll();
  }

  /**
   * Automobilių kiekio radimas
   * @return type
   */
  public static function getCarListCount() {
    $query = "SELECT
        COUNT(`" . DB_PREFIX . "automobiliai`.`id`) AS `kiekis`
      FROM
        `" . DB_PREFIX . "automobiliai`
      LEFT JOIN `" . DB_PREFIX . "modeliai`
        ON `" . DB_PREFIX . "automobiliai`.`fk_modelis` = `" . DB_PREFIX . "modeliai`.`id`
      LEFT JOIN `" . DB_PREFIX . "markes`
        ON `" . DB_PREFIX . "modeliai`.`fk_marke` = `" . DB_PREFIX . "markes`.`id`
      LEFT JOIN `" . DB_PREFIX . "auto_busenos`
        ON `" . DB_PREFIX . "automobiliai`.`busena` = `" . DB_PREFIX . "auto_busenos`.`id`";

    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Automobilio šalinimas
   * @param type $id
   */
  public static function deleteCar($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "automobiliai` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Didžiausios automobilio id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfCar() {
    $query = "SELECT MAX(`id`) as `latestId` FROM `" . DB_PREFIX . "automobiliai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

  /**
   * Pavarų dėžių sąrašo išrinkimas
   * @return type
   */
  public static function getGearboxList() {
    $query = "SELECT * FROM `" . DB_PREFIX . "pavaru_dezes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Degalų tipo sąrašo išrinkimas
   * @return type
   */
  public static function getFuelTypeList() {
    $query = "SELECT * FROM `" . DB_PREFIX . "degalu_tipai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Kėbulo tipų sąrašo išrinkimas
   * @return type
   */
  public static function getBodyTypeList() {
    $query = "SELECT * FROM `" . DB_PREFIX . "kebulu_tipai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Bagažo tipų sąrašo išrinkimas
   * @return type
   */
  public static function getLuggageTypeList() {
    $query = "SELECT * FROM `" . DB_PREFIX . "lagaminai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Automobilio būsenų sąrašo išrinkimas
   * @return type
   */
  public static function getCarStateList() {
    $query = "SELECT * FROM `" . DB_PREFIX . "auto_busenos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

}


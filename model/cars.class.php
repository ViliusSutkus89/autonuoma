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
        `automobiliai`.`id`,
        `automobiliai`.`valstybinis_nr`,
        `automobiliai`.`pagaminimo_data`,
        `automobiliai`.`rida`,
        `automobiliai`.`radijas`,
        `automobiliai`.`grotuvas`,
        `automobiliai`.`kondicionierius`,
        `automobiliai`.`vietu_skaicius`,
        `automobiliai`.`registravimo_data`,
        `automobiliai`.`verte`,
        `automobiliai`.`pavaru_deze`,
        `automobiliai`.`degalu_tipas`,
        `automobiliai`.`kebulas`,
        `automobiliai`.`bagazo_dydis`,
        `automobiliai`.`busena`,
        `automobiliai`.`fk_modelis` AS `modelis`
      FROM `automobiliai`
      WHERE `automobiliai`.`id`= ?";

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
    UPDATE `automobiliai`
    SET
      `valstybinis_nr`= ?,
      `pagaminimo_data`= ?,
      `rida`= ?,
      `radijas`= ?,
      `grotuvas`= ?,
      `kondicionierius`= ?,
      `vietu_skaicius`= ?,
      `registravimo_data`= ?,
      `verte`= ?,
      `pavaru_deze`= ?,
      `degalu_tipas`= ?,
      `kebulas`= ?,
      `bagazo_dydis`= ?,
      `busena`= ?,
      `fk_modelis`= ?
    WHERE `id`= ?";
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
    $query = "INSERT INTO `automobiliai` (
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
        `automobiliai`.`id`,
        `automobiliai`.`valstybinis_nr`,
        `auto_busenos`.`name` AS `busena`,
        `modeliai`.`pavadinimas` AS `modelis`,
        `markes`.`pavadinimas` AS `marke`
      FROM
        `automobiliai`
      LEFT JOIN `modeliai`
        ON `automobiliai`.`fk_modelis`=`modeliai`.`id`
      LEFT JOIN `markes`
        ON `modeliai`.`fk_marke`=`markes`.`id`
      LEFT JOIN `auto_busenos`
        ON `automobiliai`.`busena`=`auto_busenos`.`id`";
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
        COUNT(`automobiliai`.`id`) AS `kiekis`
      FROM
        `automobiliai`
      LEFT JOIN `modeliai`
        ON `automobiliai`.`fk_modelis`=`modeliai`.`id`
      LEFT JOIN `markes` 
        ON `modeliai`.`fk_marke`=`markes`.`id`
      LEFT JOIN `auto_busenos`
        ON `automobiliai`.`busena`=`auto_busenos`.`id`";

    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Automobilio šalinimas
   * @param type $id
   */
  public static function deleteCar($id) {
    $query = "DELETE FROM `automobiliai` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
  }

  /**
   * Sutačių, į kurias įtrauktas automobilis, kiekio radimas
   * @param type $id
   * @return type
   */
  public static function getContractCountOfCar($id) {
    $query = "SELECT
        COUNT(`sutartys`.`nr`) AS `kiekis`
      FROM
        `automobiliai`
      INNER JOIN `sutartys`
        ON `automobiliai`.`id`=`sutartys`.`fk_automobilis`
      WHERE `automobiliai`.`id`= ?";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Didžiausios automobilio id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfCar() {
    $query = "SELECT MAX(`id`) as `latestId` FROM `automobiliai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

  /**
   * Pavarų dėžių sąrašo išrinkimas
   * @return type
   */
  public static function getGearboxList() {
    $query = "SELECT * FROM `pavaru_dezes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Degalų tipo sąrašo išrinkimas
   * @return type
   */
  public static function getFuelTypeList() {
    $query = "SELECT * FROM `degalu_tipai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Kėbulo tipų sąrašo išrinkimas
   * @return type
   */
  public static function getBodyTypeList() {
    $query = "SELECT * FROM `kebulu_tipai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Bagažo tipų sąrašo išrinkimas
   * @return type
   */
  public static function getLuggageTypeList() {
    $query = "SELECT * FROM `lagaminai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Automobilio būsenų sąrašo išrinkimas
   * @return type
   */
  public static function getCarStateList() {
    $query = "SELECT * FROM `auto_busenos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

}


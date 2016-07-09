<?php

/**
 * Automobilių modelių redagavimo klasė
 *
 * @author ISK
 */

class models {

  /**
   * Modelio išrinkimas
   * @param type $id
   * @return type
   */
  public static function getModel($id) {
    $query = "SELECT * FROM `" . DB_PREFIX . "modeliai` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();

    if (count($data) == 0) {
      return false;
    }
    return $data[0];
  }

  /**
   * Modelių sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getModelList($limit = null, $offset = null) {
    $parameters = array();

    $query = "SELECT
      `" . DB_PREFIX . "modeliai`.`id`,
      `" . DB_PREFIX . "modeliai`.`pavadinimas`,
      `" . DB_PREFIX . "markes`.`pavadinimas` AS `marke`
      FROM `" . DB_PREFIX . "modeliai`
      LEFT JOIN `" . DB_PREFIX . "markes`
        ON `" . DB_PREFIX . "modeliai`.`fk_marke` = `" . DB_PREFIX . "markes`.`id`";

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
   * Modelių kiekio radimas
   * @return type
   */
  public static function getModelListCount() {
    $query = "SELECT
        COUNT(`" . DB_PREFIX . "modeliai`.`id`) AS `kiekis`
      FROM `" . DB_PREFIX . "modeliai`
      LEFT JOIN `" . DB_PREFIX . "markes`
        ON `" . DB_PREFIX . "modeliai`.`fk_marke` = `" . DB_PREFIX . "markes`.`id`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  public static function getBrandsAndModels() {
    $query = "SELECT
      `" . DB_PREFIX . "modeliai`.`id`,
      `" . DB_PREFIX . "modeliai`.`pavadinimas` as `modelis`,
      `" . DB_PREFIX . "markes`.`pavadinimas` as `marke`
    FROM
      `" . DB_PREFIX . "markes`
    LEFT JOIN
      `" . DB_PREFIX . "modeliai`
      ON `" . DB_PREFIX . "modeliai`.`fk_marke` = `" . DB_PREFIX . "markes`.`id`
    ORDER BY `marke`, `modelis`
      ";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute();
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Modelio atnaujinimas
   * @param type $data
   */
  public static function updateModel($data) {
    $query = "UPDATE `" . DB_PREFIX . "modeliai` SET `pavadinimas` = ?, `fk_marke` = ? WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['pavadinimas'], $data['fk_marke'], $data['id']
    ));
  }

  /**
   * Modelio įrašymas
   * @param type $data
   */
  public static function insertModel($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "modeliai` (`id`, `pavadinimas`, `fk_marke`) VALUES (?, ?, ?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['id'], $data['pavadinimas'], $data['fk_marke']
    ));
  }

  /**
   * Modelio šalinimas
   * @param type $id
   */
  public static function deleteModel($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "modeliai` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Didžiausios modelio id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfModel() {
    $query = "SELECT MAX(`id`) AS `latestId` FROM `" . DB_PREFIX . "modeliai`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

}


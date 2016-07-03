<?php

/**
 * Automobilių markių redagavimo klasė
 *
 * @author ISK
 */

class brands {

  /**
   * Markės išrinkimas
   * @param type $id
   * @return type
   */
  public static function getBrand($id) {
    $query = "SELECT * FROM `markes` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();

    if (count($data) == 0) {
      return false;
    }

    return $data[0];
  }

  /**
   * Markių sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getBrandList($limit = null, $offset = null) {
    $query = "SELECT * FROM `markes`";
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
   * Markių kiekio radimas
   * @return type
   */
  public static function getBrandListCount() {
    $query = "SELECT COUNT(`id`) as `kiekis` FROM `markes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Markės įrašymas
   * @param type $data
   */
  public static function insertBrand($data) {
    $query = "INSERT INTO `markes` (`id`, `pavadinimas`) VALUES (?,?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['id'], $data['pavadinimas']));
  }

  /**
   * Markės atnaujinimas
   * @param type $data
   */
  public static function updateBrand($data) {
    $query = "UPDATE `markes` SET `pavadinimas` = ? WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['pavadinimas'], $data['id']));
  }

  /**
   * Markės šalinimas
   * @param type $id
   */
  public static function deleteBrand($id) {
    $query = "DELETE FROM `markes` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
  }

  /**
   * Markės modelių kiekio radimas
   * @param type $id
   * @return type
   */
  public static function getModelCountOfBrand($id) {
    $query = "
    SELECT
      COUNT(`modeliai`.`id`) as `kiekis`
    FROM `markes`
      INNER JOIN `modeliai`
        ON `markes`.`id` = `modeliai`.`fk_marke`
    WHERE `markes`.`id` = ?
    ";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Didžiausiausios markės id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfBrand() {
    $query = "SELECT MAX(`id`) as `latestId` FROM `markes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

}


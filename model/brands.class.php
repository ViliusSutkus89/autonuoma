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
    $query = "SELECT * FROM `" . DB_PREFIX . "markes` WHERE `id` = ?";
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
    $query = "SELECT * FROM `" . DB_PREFIX . "markes`";
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
    $query = "SELECT COUNT(`id`) as `kiekis` FROM `" . DB_PREFIX . "markes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  /**
   * Markės įrašymas
   * @param type $data
   */
  public static function insertBrand($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "markes` (`id`, `pavadinimas`) VALUES (?,?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['id'], $data['pavadinimas']));
  }

  /**
   * Markės atnaujinimas
   * @param type $data
   */
  public static function updateBrand($data) {
    $query = "UPDATE `" . DB_PREFIX . "markes` SET `pavadinimas` = ? WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['pavadinimas'], $data['id']));
  }

  /**
   * Markės šalinimas
   * @param type $id
   */
  public static function deleteBrand($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "markes` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Didžiausiausios markės id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfBrand() {
    $query = "SELECT MAX(`id`) as `latestId` FROM `" . DB_PREFIX . "markes`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

}


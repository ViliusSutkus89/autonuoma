<?php

/**
 * Papildomų paslaugų redagavimo klasė
 *
 * @author ISK
 */

class services {

  /**
   * Paslaugų sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getServicesList($limit = null, $offset = null) {
    $query = "SELECT * FROM `" . DB_PREFIX . "paslaugos`";

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
   * Paslaugų kiekio radimas
   * @return type
   */
  public static function getServicesListCount() {
    $query = "SELECT COUNT(`id`) as `kiekis` FROM `" . DB_PREFIX . "paslaugos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['kiekis'];
  }

  public static function getPricedServices() {
    $query = "SELECT *
      FROM `" . DB_PREFIX . "paslaugos`
      LEFT JOIN `" . DB_PREFIX . "paslaugu_kainos`
        ON `" . DB_PREFIX . "paslaugos`.`id` = `" . DB_PREFIX . "paslaugu_kainos`.`fk_paslauga`
    ";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Paslaugos kainų sąrašo radimas
   * @param type $serviceId
   * @return type
   */
  public static function getServicePrices($serviceID, $isInUse) {
    $query = "SELECT
      `" . DB_PREFIX . "paslaugu_kainos`.`kaina`,
      `" . DB_PREFIX . "paslaugu_kainos`.`galioja_nuo`,
      IF(COUNT(`" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_paslauga`), '1', '0') as `naudojama_uzsakymuose`
    FROM `" . DB_PREFIX . "paslaugu_kainos`
    LEFT JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
      ON `" . DB_PREFIX . "paslaugu_kainos`.`fk_paslauga` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_paslauga`
      AND `" . DB_PREFIX . "paslaugu_kainos`.`galioja_nuo` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_kaina_galioja_nuo`

    WHERE `" . DB_PREFIX . "paslaugu_kainos`.`fk_paslauga` = ?
    GROUP BY `" . DB_PREFIX . "paslaugu_kainos`.`galioja_nuo`
    HAVING `naudojama_uzsakymuose` = ?
    ORDER BY `" . DB_PREFIX . "paslaugu_kainos`.`galioja_nuo`
    ";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($serviceID, $isInUse));
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Paslaugos išrinkimas
   * @param type $id
   * @return type
   */
  public static function getService($id) {
    $query = "SELECT * FROM `" . DB_PREFIX . "paslaugos` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
    if (count($data) == 0) {
      return false;
    }
    return $data[0];
  }

  /**
   * Paslaugos įrašymas
   * @param type $data
   */
  public static function insertService($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "paslaugos` (`id`, `pavadinimas`, `aprasymas`) VALUES (?, ?, ?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['id'], $data['pavadinimas'], $data['aprasymas']
    ));
  }

  /**
   * Paslaugos atnaujinimas
   * @param type $data
   */
  public static function updateService($data) {
    $query = "UPDATE `" . DB_PREFIX . "paslaugos` SET `pavadinimas` = ?, `aprasymas` = ? WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['pavadinimas'], $data['aprasymas'], $data['id']
    ));
  }

  /**
   * Paslaugos šalinimas
   * @param type $id
   */
  public static function deleteService($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "paslaugos` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    try {
      $stmt->execute(array($id));
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Paslaugos kainų įrašymas
   * @param type $data
   */
  public static function insertServicePrices($data) {
    if (empty($data['kaina']))
      return;

    $query = "INSERT IGNORE INTO `" . DB_PREFIX . "paslaugu_kainos` (`fk_paslauga`, `galioja_nuo`, `kaina`) VALUES ";
    $parameters = array();

    foreach (array_keys($data['kaina']) as $key) {
      $query .= "(?, ?, ?),";

      $parameters[] = $data['id'];
      $parameters[] = $data['galioja_nuo'][$key];
      $parameters[] = $data['kaina'][$key];
    }

    $query = rtrim($query, ",");
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
  }

  /**
   * Paslaugos kainų šalinimas
   * @param type $serviceId
   */
  public static function deleteServicePrices($serviceId) {
    $query = "DELETE FROM `" . DB_PREFIX . "paslaugu_kainos` WHERE `fk_paslauga` = ?";

    // Make sure not to delete prices that are in use by contracts
    $query .= " AND `" . DB_PREFIX . "paslaugu_kainos`.`galioja_nuo` NOT IN (
      SELECT
        `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_kaina_galioja_nuo`
      FROM
        `" . DB_PREFIX . "uzsakytos_paslaugos`
      WHERE
        `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_paslauga` = `" . DB_PREFIX . "paslaugu_kainos`.`fk_paslauga`
    )";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($serviceId));
  }

  /**
   * Didžiausios paslaugos id reikšmės radimas
   * @return type
   */
  public static function getMaxIdOfService() {
    $query = "SELECT MAX(`id`) AS `latestId` FROM `" . DB_PREFIX . "paslaugos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data[0]['latestId'];
  }

  public static function getOrderedServices($dateFrom, $dateTo) {
    $whereClauseString = "";
    $parameters = array();

    if(!empty($dateFrom)) {
      $whereClauseString .= " WHERE `" . DB_PREFIX . "sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " WHERE `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        `id`,
        `pavadinimas`,
        SUM(`kiekis`) AS `uzsakyta`,
        SUM(`kiekis`*`" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina`) AS `bendra_suma`
      FROM `" . DB_PREFIX . "paslaugos`
      INNER JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
        ON `" . DB_PREFIX . "paslaugos`.`id` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_paslauga`
      INNER JOIN `" . DB_PREFIX . "sutartys`
        ON `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis` = `" . DB_PREFIX . "sutartys`.`nr`
      {$whereClauseString}
      GROUP BY `" . DB_PREFIX . "paslaugos`.`id`
      ORDER BY `bendra_suma` DESC";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getStatsOfOrderedServices($dateFrom, $dateTo) {
    $whereClauseString = "";
    $parameters = array();

    if(!empty($dateFrom)) {
      $whereClauseString .= " WHERE `" . DB_PREFIX . "sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " WHERE `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        SUM(`kiekis`) AS `uzsakyta`,
        SUM(`kiekis`*`" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina`) AS `bendra_suma`
      FROM `" . DB_PREFIX . "paslaugos`
      INNER JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
        ON `" . DB_PREFIX . "paslaugos`.`id` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_paslauga`
      INNER JOIN `" . DB_PREFIX . "sutartys`
        ON `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis` = `" . DB_PREFIX . "sutartys`.`nr`
      {$whereClauseString}";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }
}


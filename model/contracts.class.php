<?php

/**
 * Sutarčių redagavimo klasė
 *
 * @author ISK
 */

class contracts {

  /**
   * Sutarčių sąrašo išrinkimas
   * @param type $limit
   * @param type $offset
   * @return type
   */
  public static function getContractList($limit, $offset) {
    $query = "SELECT
        `" . DB_PREFIX . "sutartys`.`nr`,
        `" . DB_PREFIX . "sutartys`.`sutarties_data`,
        `" . DB_PREFIX . "darbuotojai`.`vardas` AS `darbuotojo_vardas`,
        `" . DB_PREFIX . "darbuotojai`.`pavarde` AS `darbuotojo_pavarde`,
        `" . DB_PREFIX . "klientai`.`vardas` AS `kliento_vardas`,
        `" . DB_PREFIX . "klientai`.`pavarde` AS `kliento_pavarde`,
        `" . DB_PREFIX . "sutarties_busenos`.`name` AS `busena`
      FROM `" . DB_PREFIX . "sutartys`
      LEFT JOIN `" . DB_PREFIX . "darbuotojai`
        ON `" . DB_PREFIX . "sutartys`.`fk_darbuotojas` = `" . DB_PREFIX . "darbuotojai`.`tabelio_nr`
      LEFT JOIN `" . DB_PREFIX . "klientai`
        ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
      LEFT JOIN `" . DB_PREFIX . "sutarties_busenos`
        ON `" . DB_PREFIX . "sutartys`.`busena` = `" . DB_PREFIX . "sutarties_busenos`.`id`";
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
   * Sutarčių kiekio radimas
   * @return type
   */
  public static function getContractListCount() {
    $query = "SELECT
        COUNT(`" . DB_PREFIX . "sutartys`.`nr`) AS `kiekis`
      FROM `" . DB_PREFIX . "sutartys`
      LEFT JOIN `" . DB_PREFIX . "darbuotojai`
        ON `" . DB_PREFIX . "sutartys`.`fk_darbuotojas` = `" . DB_PREFIX . "darbuotojai`.`tabelio_nr`
      LEFT JOIN `" . DB_PREFIX . "klientai`
        ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
      LEFT JOIN `" . DB_PREFIX . "sutarties_busenos`
        ON `" . DB_PREFIX . "sutartys`.`busena` = `" . DB_PREFIX . "sutarties_busenos`.`id`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();

    return $data[0]['kiekis'];
  }

  /**
   * Sutarties išrinkimas
   * @param type $id
   * @return type
   */
  public static function getContract($id) {
    $query = "SELECT
        `" . DB_PREFIX . "sutartys`.`nr`,
        `" . DB_PREFIX . "sutartys`.`sutarties_data`,
        `" . DB_PREFIX . "sutartys`.`nuomos_data_laikas`,
        `" . DB_PREFIX . "sutartys`.`planuojama_grazinimo_data_laikas`,
        `" . DB_PREFIX . "sutartys`.`faktine_grazinimo_data_laikas`,
        `" . DB_PREFIX . "sutartys`.`pradine_rida`,
        `" . DB_PREFIX . "sutartys`.`galine_rida`,
        `" . DB_PREFIX . "sutartys`.`kaina`,
        `" . DB_PREFIX . "sutartys`.`degalu_kiekis_paimant`,
        `" . DB_PREFIX . "sutartys`.`dagalu_kiekis_grazinus`,
        `" . DB_PREFIX . "sutartys`.`busena`,
        `" . DB_PREFIX . "sutartys`.`fk_klientas`,
        `" . DB_PREFIX . "sutartys`.`fk_darbuotojas`,
        `" . DB_PREFIX . "sutartys`.`fk_automobilis`,
        `" . DB_PREFIX . "sutartys`.`fk_grazinimo_vieta`,
        `" . DB_PREFIX . "sutartys`.`fk_paemimo_vieta`,
        (IFNULL(SUM(`" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina` * `" . DB_PREFIX . "uzsakytos_paslaugos`.`kiekis`), 0) + `" . DB_PREFIX . "sutartys`.`kaina`)
          AS `bendra_kaina`
      FROM `" . DB_PREFIX . "sutartys`
      LEFT JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
        ON `" . DB_PREFIX . "sutartys`.`nr` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis`
      WHERE
        `" . DB_PREFIX . "sutartys`.`nr` = ?
      GROUP BY
        `" . DB_PREFIX . "sutartys`.`nr`";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
    if (count($data) == 0) {
      return false;
    }
    return $data[0];
  }

  /**
   * Užsakytų papildomų paslaugų sąrašo išrinkimas
   * @param type $orderId
   * @return type
   */
  public static function getOrderedServices($orderId) {
    $query = "SELECT * FROM `" . DB_PREFIX . "uzsakytos_paslaugos` WHERE `fk_sutartis` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($orderId));
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Sutarties atnaujinimas
   * @param type $data
   */
  public static function updateContract($data) {
    $query = "UPDATE `" . DB_PREFIX . "sutartys` SET
        `sutarties_data` = ?,
        `nuomos_data_laikas` = ?,
        `planuojama_grazinimo_data_laikas` = ?,
        `faktine_grazinimo_data_laikas` = ?,
        `pradine_rida` = ?,
        `galine_rida` = ?,
        `kaina` = ?,
        `degalu_kiekis_paimant` = ?,
        `dagalu_kiekis_grazinus` = ?,
        `busena` = ?,
        `fk_klientas` = ?,
        `fk_darbuotojas` = ?,
        `fk_automobilis` = ?,
        `fk_grazinimo_vieta` = ?,
        `fk_paemimo_vieta` = ?
      WHERE `nr` = ?";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['sutarties_data'],
      $data['nuomos_data_laikas'],
      $data['planuojama_grazinimo_data_laikas'],
      $data['faktine_grazinimo_data_laikas'],
      $data['pradine_rida'],
      $data['galine_rida'],
      $data['kaina'],
      $data['degalu_kiekis_paimant'],
      $data['dagalu_kiekis_grazinus'],
      $data['busena'],
      $data['fk_klientas'],
      $data['fk_darbuotojas'],
      $data['fk_automobilis'],
      $data['fk_grazinimo_vieta'],
      $data['fk_paemimo_vieta'],
      $data['nr']
    ));
  }

  /**
   * Sutarties įrašymas
   * @param type $data
   */
  public static function insertContract($data) {
    $query = "INSERT INTO `" . DB_PREFIX . "sutartys`
      (
        `nr`,
        `sutarties_data`,
        `nuomos_data_laikas`,
        `planuojama_grazinimo_data_laikas`,
        `faktine_grazinimo_data_laikas`,
        `pradine_rida`,
        `galine_rida`,
        `kaina`,
        `degalu_kiekis_paimant`,
        `dagalu_kiekis_grazinus`,
        `busena`,
        `fk_klientas`,
        `fk_darbuotojas`,
        `fk_automobilis`,
        `fk_grazinimo_vieta`,
        `fk_paemimo_vieta`
      ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysql::getInstance()->prepare($query);
    $parameters = array(
      $data['nr'],
      $data['sutarties_data'],
      $data['nuomos_data_laikas'],
      $data['planuojama_grazinimo_data_laikas'],
      $data['faktine_grazinimo_data_laikas'],
      $data['pradine_rida'],
      $data['galine_rida'],
      $data['kaina'],
      $data['degalu_kiekis_paimant'],
      $data['dagalu_kiekis_grazinus'],
      $data['busena'],
      $data['fk_klientas'],
      $data['fk_darbuotojas'],
      $data['fk_automobilis'],
      $data['fk_grazinimo_vieta'],
      $data['fk_paemimo_vieta']
    );
    try {
      $stmt->execute($parameters);
    } catch (PDOException $e) {
      return false;
    }
    return true;
  }

  /**
   * Sutarties šalinimas
   * @param type $id
   */
  public static function deleteContract($id) {
    $query = "DELETE FROM `" . DB_PREFIX . "sutartys` WHERE `nr` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
  }

  /**
   * Užsakytų papildomų paslaugų šalinimas
   * @param type $contractId
   * @param type $keep
   */
  public static function deleteOrderedServices($contractId, $keep = array()) {
    $keepQuery = array();
    $parameters = array($contractId);

    foreach ($keep as $var) {
      $keepQuery[] = "(?, ?)";
      $parameters[] = $var['fk_kaina_galioja_nuo'];
      $parameters[] = $var['fk_paslauga'];
    }

    $query = "DELETE FROM `" . DB_PREFIX . "uzsakytos_paslaugos` WHERE `fk_sutartis` = ?";
    if (count($keepQuery)) {
      $query .= "AND
        (`fk_kaina_galioja_nuo`, `fk_paslauga`) NOT IN
        (". implode(",", $keepQuery) . ")";
    }

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
  }


  /**
   * Užsakytų papildomų paslaugų atnaujinimas
   * @param type $data
   */
  public static function updateOrderedServices($data) {
    // Sanity check, make sure we have an array to work with
    if (empty($data['paslaugos']))
      $data['paslaugos'] = array();

    $valuesQuery = array();
    $parameters = array();
    $keep = array();

    foreach($data['paslaugos'] as $key => $val) {
      $tmp = explode(":", $val);
      $serviceId = $tmp[0];
      $price = $tmp[1];
      $date_from = $tmp[2];

      $valuesQuery[] = "(?, ?, ?, ?, ?)";
      $parameters[] = $data['nr'];
      $parameters[] = $date_from;
      $parameters[] = $serviceId;
      $parameters[] = $data['kiekiai'][$key];
      $parameters[] = $price;

      $keep[] = array(
        'fk_kaina_galioja_nuo'  => $date_from,
        'fk_paslauga'           => $serviceId,
      );
    }

    self::deleteOrderedServices($data['nr'], $keep);
    if (count($valuesQuery)) {
      $valuesQuery = implode(",", $valuesQuery);

      $query = "INSERT INTO `" . DB_PREFIX . "uzsakytos_paslaugos`
        (
          `fk_sutartis`,
          `fk_kaina_galioja_nuo`,
          `fk_paslauga`,
          `kiekis`,
          `kaina`
        )
        VALUES
        {$valuesQuery}
        ON DUPLICATE KEY UPDATE
          `kiekis` = VALUES(`kiekis`),
          `kaina` = VALUES(`kaina`)
      ";

      $stmt = mysql::getInstance()->prepare($query);
      $stmt->execute($parameters);
    }
  }

  /**
   * Sutarties būsenų sąrašo išrinkimas
   * @return type
   */
  public static function getContractStates() {
    $query = "SELECT * FROM `" . DB_PREFIX . "sutarties_busenos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Aikštelių sąrašo išrinkimas
   * @return type
   */
  public static function getParkingLots() {
    $query = "SELECT * FROM `" . DB_PREFIX . "aiksteles`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getCustomerContracts($dateFrom, $dateTo) {
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

    // $whereClauseString is used three times in this query
    // We need it to have the same amount of parameters.
    $parameters = array_merge($parameters, $parameters, $parameters);

    $query = "SELECT
        `" . DB_PREFIX . "sutartys`.`nr`,
        `" . DB_PREFIX . "sutartys`.`sutarties_data`,
        `" . DB_PREFIX . "klientai`.`asmens_kodas`,
        `" . DB_PREFIX . "klientai`.`vardas`,
        `" . DB_PREFIX . "klientai`.`pavarde`,
        `" . DB_PREFIX . "sutartys`.`kaina` as `sutarties_kaina`,
        IFNULL(sum(`" . DB_PREFIX . "uzsakytos_paslaugos`.`kiekis` * `" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina`), 0)
          AS `sutarties_paslaugu_kaina`,
        `t`.`bendra_kliento_sutarciu_kaina`,
        `s`.`bendra_kliento_paslaugu_kaina`
      FROM `" . DB_PREFIX . "sutartys`

      INNER JOIN `" . DB_PREFIX . "klientai`
        ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`

      LEFT JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
        ON `" . DB_PREFIX . "sutartys`.`nr` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis`

      LEFT JOIN (
        SELECT
          `asmens_kodas`,
          SUM(`" . DB_PREFIX . "sutartys`.`kaina`) AS `bendra_kliento_sutarciu_kaina`
        FROM `" . DB_PREFIX . "sutartys`
        INNER JOIN `" . DB_PREFIX . "klientai`
          ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
        {$whereClauseString}
        GROUP BY `asmens_kodas`
      ) `t`
        ON `t`.`asmens_kodas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`

      LEFT JOIN (
        SELECT
          `asmens_kodas`,
          IFNULL(sum(`" . DB_PREFIX . "uzsakytos_paslaugos`.`kiekis` * `" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina`), 0)
            AS `bendra_kliento_paslaugu_kaina`
        FROM `" . DB_PREFIX . "sutartys`
        INNER JOIN `" . DB_PREFIX . "klientai`
          ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
        LEFT JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
          ON `" . DB_PREFIX . "sutartys`.`nr` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis`
        {$whereClauseString}							
        GROUP BY `asmens_kodas`
      ) `s`
        ON `s`.`asmens_kodas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
      {$whereClauseString}
      GROUP BY `" . DB_PREFIX . "sutartys`.`nr`
      ORDER BY `" . DB_PREFIX . "klientai`.`pavarde` ASC";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getSumPriceOfContracts($dateFrom, $dateTo) {
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
        SUM(`" . DB_PREFIX . "sutartys`.`kaina`) AS `nuomos_suma`
      FROM `" . DB_PREFIX . "sutartys`
      {$whereClauseString}";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getSumPriceOfOrderedServices($dateFrom, $dateTo) {
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
        SUM(`" . DB_PREFIX . "uzsakytos_paslaugos`.`kiekis` * `" . DB_PREFIX . "uzsakytos_paslaugos`.`kaina`)
          AS `paslaugu_suma`
      FROM `" . DB_PREFIX . "sutartys`
      INNER JOIN `" . DB_PREFIX . "uzsakytos_paslaugos`
        ON `" . DB_PREFIX . "sutartys`.`nr` = `" . DB_PREFIX . "uzsakytos_paslaugos`.`fk_sutartis`
      {$whereClauseString}";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getDelayedCars($dateFrom, $dateTo) {
    $whereClauseString = "";
    $parameters = array();

    if(!empty($dateFrom)) {
      $whereClauseString .= " AND `" . DB_PREFIX . "sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `" . DB_PREFIX . "sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        `nr`,
        `sutarties_data`,
        `planuojama_grazinimo_data_laikas`,
        IF(`faktine_grazinimo_data_laikas` = '0000-00-00 00:00:00', 'negrąžinta', `faktine_grazinimo_data_laikas`)
          AS `grazinta`,
        `" . DB_PREFIX . "klientai`.`vardas`,
        `" . DB_PREFIX . "klientai`.`pavarde`
      FROM `" . DB_PREFIX . "sutartys`
      INNER JOIN `" . DB_PREFIX . "klientai`
        ON `" . DB_PREFIX . "sutartys`.`fk_klientas` = `" . DB_PREFIX . "klientai`.`asmens_kodas`
      WHERE (
          DATEDIFF(`faktine_grazinimo_data_laikas`, `planuojama_grazinimo_data_laikas`) >= 1
        OR (
            `faktine_grazinimo_data_laikas` = '0000-00-00 00:00:00'
          AND
            DATEDIFF(NOW(), `planuojama_grazinimo_data_laikas`) >= 1
        )
      )
      {$whereClauseString}";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

}


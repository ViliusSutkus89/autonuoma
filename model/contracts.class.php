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
        `sutartys`.`nr`,
        `sutartys`.`sutarties_data`,
        `darbuotojai`.`vardas` AS `darbuotojo_vardas`,
        `darbuotojai`.`pavarde` AS `darbuotojo_pavarde`,
        `klientai`.`vardas` AS `kliento_vardas`,
        `klientai`.`pavarde` AS `kliento_pavarde`,
        `sutarties_busenos`.`name` AS `busena`
      FROM `sutartys`
      LEFT JOIN `darbuotojai`
        ON `sutartys`.`fk_darbuotojas`=`darbuotojai`.`tabelio_nr`
      LEFT JOIN `klientai`
        ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
      LEFT JOIN `sutarties_busenos`
        ON `sutartys`.`busena`=`sutarties_busenos`.`id`";
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
        COUNT(`sutartys`.`nr`) AS `kiekis`
      FROM `sutartys`
      LEFT JOIN `darbuotojai`
        ON `sutartys`.`fk_darbuotojas`=`darbuotojai`.`tabelio_nr`
      LEFT JOIN `klientai`
        ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
      LEFT JOIN `sutarties_busenos`
        ON `sutartys`.`busena`=`sutarties_busenos`.`id`";
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
        `sutartys`.`nr`,
        `sutartys`.`sutarties_data`,
        `sutartys`.`nuomos_data_laikas`,
        `sutartys`.`planuojama_grazinimo_data_laikas`,
        `sutartys`.`faktine_grazinimo_data_laikas`,
        `sutartys`.`pradine_rida`,
        `sutartys`.`galine_rida`,
        `sutartys`.`kaina`,
        `sutartys`.`degalu_kiekis_paimant`,
        `sutartys`.`dagalu_kiekis_grazinus`,
        `sutartys`.`busena`,
        `sutartys`.`fk_klientas`,
        `sutartys`.`fk_darbuotojas`,
        `sutartys`.`fk_automobilis`,
        `sutartys`.`fk_grazinimo_vieta`,
        `sutartys`.`fk_paemimo_vieta`,
        (IFNULL(SUM(`uzsakytos_paslaugos`.`kaina` * `uzsakytos_paslaugos`.`kiekis`), 0) + `sutartys`.`kaina`)
          AS `bendra_kaina`
      FROM `sutartys`
      LEFT JOIN `uzsakytos_paslaugos`
        ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
      WHERE
        `sutartys`.`nr`= ?
      GROUP BY
        `sutartys`.`nr`";

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
    $query = "SELECT * FROM `uzsakytos_paslaugos` WHERE `fk_sutartis`= ?";
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
    $query = "UPDATE `sutartys` SET
        `sutarties_data`= ?,
        `nuomos_data_laikas`= ?,
        `planuojama_grazinimo_data_laikas`= ?,
        `faktine_grazinimo_data_laikas`= ?,
        `pradine_rida`= ?,
        `galine_rida`= ?,
        `kaina`= ?,
        `degalu_kiekis_paimant`= ?,
        `dagalu_kiekis_grazinus`= ?,
        `busena`= ?,
        `fk_klientas`= ?,
        `fk_darbuotojas`= ?,
        `fk_automobilis`= ?,
        `fk_grazinimo_vieta`= ?,
        `fk_paemimo_vieta`= ?
      WHERE `nr`= ?";

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
    $query = "INSERT INTO `sutartys`
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
    $stmt->execute(array(
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
    ));
  }

  /**
   * Sutarties šalinimas
   * @param type $id
   */
  public static function deleteContract($id) {
    $query = "DELETE FROM `sutartys` WHERE `nr`=?";
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

    $query = "
      DELETE FROM
        `uzsakytos_paslaugos`
      WHERE
        `fk_sutartis` = ?
      ";
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

      $query = "INSERT INTO `uzsakytos_paslaugos`
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
    $query = "SELECT * FROM `sutarties_busenos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  /**
   * Aikštelių sąrašo išrinkimas
   * @return type
   */
  public static function getParkingLots() {
    $query = "SELECT * FROM `aiksteles`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getCustomerContracts($dateFrom, $dateTo) {
    $whereClauseString = "";
    $parameters = array();

    if(!empty($dateFrom)) {
      $whereClauseString .= " WHERE `sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " WHERE `sutartys`.`sutarties_data`<= ?";
        $parameters[] = $dateTo;
      }
    }

    // $whereClauseString is used three times in this query
    // We need it to have the same amount of parameters.
    $parameters = array_merge($parameters, $parameters, $parameters);

    $query = "SELECT
        `sutartys`.`nr`,
        `sutartys`.`sutarties_data`,
        `klientai`.`asmens_kodas`,
        `klientai`.`vardas`,
        `klientai`.`pavarde`,
        `sutartys`.`kaina` as `sutarties_kaina`,
        IFNULL(sum(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`), 0)
          AS `sutarties_paslaugu_kaina`,
        `t`.`bendra_kliento_sutarciu_kaina`,
        `s`.`bendra_kliento_paslaugu_kaina`
      FROM `sutartys`

      INNER JOIN `klientai`
        ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`

      LEFT JOIN `uzsakytos_paslaugos`
        ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`

      LEFT JOIN (
        SELECT
          `asmens_kodas`,
          SUM(`sutartys`.`kaina`) AS `bendra_kliento_sutarciu_kaina`
        FROM `sutartys`
        INNER JOIN `klientai`
          ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
        {$whereClauseString}
        GROUP BY `asmens_kodas`
      ) `t`
        ON `t`.`asmens_kodas`=`klientai`.`asmens_kodas`

      LEFT JOIN (
        SELECT
          `asmens_kodas`,
          IFNULL(sum(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`), 0)
            AS `bendra_kliento_paslaugu_kaina`
        FROM `sutartys`
        INNER JOIN `klientai`
          ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
        LEFT JOIN `uzsakytos_paslaugos`
          ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
        {$whereClauseString}							
        GROUP BY `asmens_kodas`
      ) `s`
        ON `s`.`asmens_kodas`=`klientai`.`asmens_kodas`
      {$whereClauseString}
      GROUP BY `sutartys`.`nr`
      ORDER BY `klientai`.`pavarde` ASC";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    return $data;
  }

  public static function getSumPriceOfContracts($dateFrom, $dateTo) {
    $whereClauseString = "";
    $parameters = array();

    if(!empty($dateFrom)) {
      $whereClauseString .= " WHERE `sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " WHERE `sutartys`.`sutarties_data`<=?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        SUM(`sutartys`.`kaina`) AS `nuomos_suma`
      FROM `sutartys`
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
      $whereClauseString .= " WHERE `sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " WHERE `sutartys`.`sutarties_data`<=?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        SUM(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`)
          AS `paslaugu_suma`
      FROM `sutartys`
      INNER JOIN `uzsakytos_paslaugos`
        ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
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
      $whereClauseString .= " AND `sutartys`.`sutarties_data` >= ?";
      $parameters[] = $dateFrom;
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `sutartys`.`sutarties_data` <= ?";
        $parameters[] = $dateTo;
      }
    } else {
      if(!empty($dateTo)) {
        $whereClauseString .= " AND `sutartys`.`sutarties_data`<=?";
        $parameters[] = $dateTo;
      }
    }

    $query = "SELECT
        `nr`,
        `sutarties_data`,
        `planuojama_grazinimo_data_laikas`,
        IF(`faktine_grazinimo_data_laikas`='0000-00-00 00:00:00', 'negrąžinta', `faktine_grazinimo_data_laikas`)
          AS `grazinta`,
        `klientai`.`vardas`,
        `klientai`.`pavarde`
      FROM `sutartys`
      INNER JOIN `klientai`
        ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
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


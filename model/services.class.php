<?php

/**
 * Papildomų paslaugų redagavimo klasė
 *
 * @author ISK
 */

class services {
	
	public function __construct() {
		
	}
	
	/**
	 * Paslaugų sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getServicesList($limit = null, $offset = null) {
		$query = "  SELECT *
          FROM `paslaugos`";

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
	public function getServicesListCount() {
		$query = "  SELECT COUNT(`paslaugos`.`id`) as `kiekis`
					FROM `paslaugos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
		return $data[0]['kiekis'];
	}
	
	/**
	 * Paslaugos kainų sąrašo radimas
	 * @param type $serviceId
	 * @return type
	 */
	public function getServicePrices($serviceIDs) {
    $IN = "";
    $parameters = array();
    foreach ($serviceIDs as $val) {
      $IN .= "?,";
      $parameters[] = $val;
    }
    $IN = rtrim($IN, ",");
    if (empty($parameters))
      return array();

		$query = "  SELECT *
					FROM `paslaugu_kainos`
          WHERE `fk_paslauga` IN (${IN})";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();
    $d = array();
    foreach ($data as $val) {
      if (empty($d[$val['fk_paslauga']]))
        $d[$val['fk_paslauga']] = array();

      $d[$val['fk_paslauga']][] = $val;
    }
		return $d;
	}
	
	/**
	 * Sutarčių, į kurias įtraukta paslauga, kiekio radimas
	 * @param type $serviceId
	 * @return type
	 */
	public function getContractCountOfService($serviceId) {
		$query = "  SELECT COUNT(`sutartys`.`nr`) AS `kiekis`
					FROM `paslaugos`
						INNER JOIN `paslaugu_kainos`
							ON `paslaugos`.`id`=`paslaugu_kainos`.`fk_paslauga`
						INNER JOIN `uzsakytos_paslaugos`
							ON `paslaugu_kainos`.`fk_paslauga`=`uzsakytos_paslaugos`.`fk_paslauga`
						INNER JOIN `sutartys`
							ON `uzsakytos_paslaugos`.`fk_sutartis`=`sutartys`.`nr`
          WHERE `paslaugos`.`id`= ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($serviceId));
    $data = $stmt->fetchAll();
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Paslaugos išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getService($id) {
		$query = "  SELECT *
					FROM `paslaugos`
          WHERE `id`= ?";

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
	public function insertService($data) {
		$query = "  INSERT INTO `paslaugos`
								(
									`id`,
									`pavadinimas`,
									`aprasymas`
								)
								VALUES
								(
                  ?, ?, ?
								)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['id'], $data['pavadinimas'], $data['aprasymas']
    ));
	}
	
	/**
	 * Paslaugos atnaujinimas
	 * @param type $data
	 */
	public function updateService($data) {
		$query = "  UPDATE `paslaugos`
					SET    `pavadinimas`= ?,
						   `aprasymas`= ?
					WHERE `id`= ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array(
      $data['pavadinimas'], $data['aprasymas'], $data['id']
    ));
	}
	
	/**
	 * Paslaugos šalinimas
	 * @param type $id
	 */
	public function deleteService($id) {
		$query = "  DELETE FROM `paslaugos`
          WHERE `id`=?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
	}
	
	/**
	 * Paslaugos kainų įrašymas
	 * @param type $data
	 */
	public function insertServicePrices($data) {
				$query = "  INSERT INTO `paslaugu_kainos`
										(
											`fk_paslauga`,
											`galioja_nuo`,
											`kaina`
										)
										VALUES ";
    $parameters = array();

		foreach($data['kainos'] as $key=>$val) {
			if($data['neaktyvus'] == array() || $data['neaktyvus'][$key] == 0) {

        $query .= "(?, ?, ?),";

			  $parameters[] = $data['id'];
			  $parameters[] = $data['datos'][$key];
			  $parameters[] = $val;
			}
		}

    if (count($parameters)) {
      $query = rtrim($query, ",");
      $stmt = mysql::getInstance()->prepare($query);
      $stmt->execute($parameters);
    }
	}
	
	/**
	 * Paslaugos kainų šalinimas
	 * @param type $serviceId
	 * @param type $clause
	 */
	public function deleteServicePrices($serviceId, $galiojaNuo = []) {
    $parameters = array($serviceId);
		$query = "  DELETE FROM `paslaugu_kainos`
					WHERE `fk_paslauga`= ?";

    foreach($galiojaNuo as $val) {
      $query .= " AND NOT `galioja_nuo` = ?";
      $parameters[] = $val;
    }

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
	}

	/**
	 * Didžiausios paslaugos id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfService() {
		$query = "  SELECT MAX(`id`) AS `latestId`
					FROM `paslaugos`";
    $stmt = mysql::getInstance()->query($query);
    $data = $stmt->fetchAll();
		
		return $data[0]['latestId'];
	}
	
	public function getOrderedServices($dateFrom, $dateTo) {
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
		
		$query = "  SELECT `id`,
						   `pavadinimas`,
						   sum(`kiekis`) AS `uzsakyta`,
						   sum(`kiekis`*`uzsakytos_paslaugos`.`kaina`) AS `bendra_suma`
					FROM `paslaugos`
						INNER JOIN `uzsakytos_paslaugos`
							ON `paslaugos`.`id`=`uzsakytos_paslaugos`.`fk_paslauga`
						INNER JOIN `sutartys`
							ON `uzsakytos_paslaugos`.`fk_sutartis`=`sutartys`.`nr`
					{$whereClauseString}
					GROUP BY `paslaugos`.`id` ORDER BY `bendra_suma` DESC";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();

		return $data;
	}

	public function getStatsOfOrderedServices($dateFrom, $dateTo) {
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
		
		$query = "  SELECT sum(`kiekis`) AS `uzsakyta`,
						   sum(`kiekis`*`uzsakytos_paslaugos`.`kaina`) AS `bendra_suma`
					FROM `paslaugos`
						INNER JOIN `uzsakytos_paslaugos`
							ON `paslaugos`.`id`=`uzsakytos_paslaugos`.`fk_paslauga`
						INNER JOIN `sutartys`
							ON `uzsakytos_paslaugos`.`fk_sutartis`=`sutartys`.`nr`
					{$whereClauseString}";

    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute($parameters);
    $data = $stmt->fetchAll();

		return $data;
	}
}


<?php

/**
 * Automobilių modelių redagavimo klasė
 *
 * @author ISK
 */

class models {
	
	public function __construct() {
		
	}
	
	/**
	 * Modelio išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getModel($id) {
		$query = "  SELECT *
					FROM `modeliai`
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
	 * Modelių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getModelList($limit = null, $offset = null) {
    $parameters = array();
		
		$query = "  SELECT `modeliai`.`id`,
						   `modeliai`.`pavadinimas`,
						    `markes`.`pavadinimas` AS `marke`
					FROM `modeliai`
						LEFT JOIN `markes`
              ON `modeliai`.`fk_marke`=`markes`.`id`";

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
	public function getModelListCount() {
		$query = "  SELECT COUNT(`modeliai`.`id`) as `kiekis`
					FROM `modeliai`
						LEFT JOIN `markes`
							ON `modeliai`.`fk_marke`=`markes`.`id`";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute();
    $data = $stmt->fetchAll();
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Modelių išrinkimas pagal markę
	 * @param type $brandId
	 * @return type
	 */
	public function getModelListByBrand($brandId) {
		$query = "  SELECT *
					FROM `modeliai`
          WHERE `fk_marke`= ?";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute(array($brandId));
    $data = $stmt->fetchAll();
		
		return $data;
	}
	
	/**
	 * Modelio atnaujinimas
	 * @param type $data
	 */
	public function updateModel($data) {
		$query = "  UPDATE `modeliai`
					SET    `pavadinimas`= ?,
						   `fk_marke`= ?
					WHERE `id`= ?";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute(array(
        $data['pavadinimas'], $data['fk_marke'], $data['id']
    ));
	}
	
	/**
	 * Modelio įrašymas
	 * @param type $data
	 */
	public function insertModel($data) {
		$query = "  INSERT INTO `modeliai`
								(
									`id`,
									`pavadinimas`,
									`fk_marke`
								)
								VALUES
								(
                  ?, ?, ?
								)";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute(array(
        $data['id'], $data['pavadinimas'], $data['fk_marke']
    ));
	}
	
	/**
	 * Modelio šalinimas
	 * @param type $id
	 */
	public function deleteModel($id) {
		$query = "  DELETE FROM `modeliai`
          WHERE `id`=?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
	}
	
	/**
	 * Nurodyto modelio automobilių kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getCarCountOfModel($id) {
		$query = "  SELECT COUNT(`automobiliai`.`id`) AS `kiekis`
					FROM `modeliai`
						INNER JOIN `automobiliai`
							ON `modeliai`.`id`=`automobiliai`.`fk_modelis`
          WHERE `modeliai`.`id`=?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Didžiausios modelio id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfModel() {
		$query = "  SELECT MAX(`id`) AS `latestId`
					FROM `modeliai`";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute();
    $data = $stmt->fetchAll();
		
		return $data[0]['latestId'];
	}
	
}

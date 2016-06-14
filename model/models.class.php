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
					WHERE `id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Modelių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getModelList($limit = null, $offset = null) {
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "  SELECT `modeliai`.`id`,
						   `modeliai`.`pavadinimas`,
						    `markes`.`pavadinimas` AS `marke`
					FROM `modeliai`
						LEFT JOIN `markes`
							ON `modeliai`.`fk_marke`=`markes`.`id` LIMIT {$limit} OFFSET {$offset}";
		$data = mysql::select($query);
		
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
		$data = mysql::select($query);
		
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
					WHERE `fk_marke`='{$brandId}'";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Modelio atnaujinimas
	 * @param type $data
	 */
	public function updateModel($data) {
		$query = "  UPDATE `modeliai`
					SET    `pavadinimas`='{$data['pavadinimas']}',
						   `fk_marke`='{$data['fk_marke']}'
					WHERE `id`='{$data['id']}'";
		mysql::query($query);
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
									'{$data['id']}',
									'{$data['pavadinimas']}',
									'{$data['fk_marke']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Modelio šalinimas
	 * @param type $id
	 */
	public function deleteModel($id) {
		$query = "  DELETE FROM `modeliai`
					WHERE `id`='{$id}'";
		mysql::query($query);
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
					WHERE `modeliai`.`id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Didžiausios modelio id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfModel() {
		$query = "  SELECT MAX(`id`) AS `latestId`
					FROM `modeliai`";
		$data = mysql::select($query);
		
		return $data[0]['latestId'];
	}
	
}
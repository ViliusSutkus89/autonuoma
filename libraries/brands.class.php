<?php

/**
 * Automobilių markių redagavimo klasė
 *
 * @author ISK
 */

class brands {

	public function __construct() {
		
	}
	
	/**
	 * Markės išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getBrand($id) {
		$query = "  SELECT *
					FROM `markes`
					WHERE `id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Markių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getBrandList($limit = null, $offset = null) {
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "  SELECT *
					FROM `markes`" . $limitOffsetString;
		$data = mysql::select($query);
		
		return $data;
	}

	/**
	 * Markių kiekio radimas
	 * @return type
	 */
	public function getBrandListCount() {
		$query = "  SELECT COUNT(`id`) as `kiekis`
					FROM `markes`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Markės įrašymas
	 * @param type $data
	 */
	public function insertBrand($data) {
		$query = "  INSERT INTO `markes`
								(
									`id`,
									`pavadinimas`
								)
								VALUES
								(
									'{$data['id']}',
									'{$data['pavadinimas']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Markės atnaujinimas
	 * @param type $data
	 */
	public function updateBrand($data) {
		$query = "  UPDATE `markes`
					SET    `pavadinimas`='{$data['pavadinimas']}'
					WHERE `id`='{$data['id']}'";
		mysql::query($query);
	}
	
	/**
	 * Markės šalinimas
	 * @param type $id
	 */
	public function deleteBrand($id) {
		$query = "  DELETE FROM `markes`
					WHERE `id`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Markės modelių kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getModelCountOfBrand($id) {
		$query = "  SELECT COUNT(`modeliai`.`id`) AS `kiekis`
					FROM `markes`
						INNER JOIN `modeliai`
							ON `markes`.`id`=`modeliai`.`fk_marke`
					WHERE `markes`.`id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Didžiausiausios markės id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfBrand() {
		$query = "  SELECT MAX(`id`) AS `latestId`
					FROM `markes`";
		$data = mysql::select($query);
		
		return $data[0]['latestId'];
	}
	
}
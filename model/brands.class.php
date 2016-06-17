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
	public function getBrandList($limit = null, $offset = null) {
    $parameters = array();

    $query = "SELECT * FROM `markes`";

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
	public function getBrandListCount() {
		$query = "SELECT COUNT(`id`) as `kiekis` FROM `markes`";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute();
    $data = $stmt->fetchAll();
		return $data[0]['kiekis'];
	}
	
	/**
	 * Markės įrašymas
	 * @param type $data
	 */
	public function insertBrand($data) {
    $query = "INSERT INTO `markes` (`id`, `pavadinimas`) VALUES (?,?)";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['id'], $data['pavadinimas']));
	}
	
	/**
	 * Markės atnaujinimas
	 * @param type $data
	 */
	public function updateBrand($data) {
    $query = "UPDATE `markes` SET `pavadinimas` = ? WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($data['pavadinimas'], $data['id']));
	}
	
	/**
	 * Markės šalinimas
	 * @param type $id
	 */
	public function deleteBrand($id) {
    $query = "DELETE FROM `markes` WHERE `id` = ?";
    $stmt = mysql::getInstance()->prepare($query);
    $stmt->execute(array($id));
	}
	
	/**
	 * Markės modelių kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getModelCountOfBrand($id) {
    $query = "
    SELECT
      COUNT(`modeliai`.`id`) as `kiekis`
    FROM `markes`
		  INNER JOIN `modeliai`
        ON `markes`.`id` = `modeliai`.`fk_marke`
    WHERE `markes`.`id` = ?
    ";

    $stmt = mysql::getInstance()->query($query);
    $stmt->execute(array($id));
    $data = $stmt->fetchAll();
		return $data[0]['kiekis'];
	}
	
	/**
	 * Didžiausiausios markės id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfBrand() {
		$query = "SELECT MAX(`id`) as `latestId` FROM `markes`";
    $stmt = mysql::getInstance()->query($query);
    $stmt->execute();
    $data = $stmt->fetchAll();
		return $data[0]['latestId'];
	}
	
}

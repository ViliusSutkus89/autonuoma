<?php

/**
 * Klientų redagavimo klasė
 *
 * @author ISK
 */

class customers {
	
	public function __construct() {
		
	}
	
	/**
	 * Kliento išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getCustomer($id) {
		$query = "  SELECT *
					FROM `klientai`
					WHERE `asmens_kodas`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Klientų sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getCustomersList($limit = null, $offset = null) {
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "  SELECT *
					FROM `klientai`" . $limitOffsetString;
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Klientų kiekio radimas
	 * @return type
	 */
	public function getCustomersListCount() {
		$query = "  SELECT COUNT(`asmens_kodas`) as `kiekis`
					FROM `klientai`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Kliento šalinimas
	 * @param type $id
	 */
	public function deleteCustomer($id) {
		$query = "  DELETE FROM `klientai`
					WHERE `asmens_kodas`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Kliento atnaujinimas
	 * @param type $data
	 */
	public function updateCustomer($data) {
		$query = "  UPDATE `klientai`
					SET    `vardas`='{$data['vardas']}',
						   `pavarde`='{$data['pavarde']}',
						   `gimimo_data`='{$data['gimimo_data']}',
						   `telefonas`='{$data['telefonas']}',
						   `epastas`='{$data['epastas']}'
					WHERE `asmens_kodas`='{$data['asmens_kodas']}'";
		mysql::query($query);
	}
	
	/**
	 * Kliento įrašymas
	 * @param type $data
	 */
	public function insertCustomer($data) {
		$query = "  INSERT INTO `klientai`
								(
									`asmens_kodas`,
									`vardas`,
									`pavarde`,
									`gimimo_data`,
									`telefonas`,
									`epastas`
								) 
								VALUES
								(
									'{$data['asmens_kodas']}',
									'{$data['vardas']}',
									'{$data['pavarde']}',
									'{$data['gimimo_data']}',
									'{$data['telefonas']}',
									'{$data['epastas']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Sutarčių, į kurias įtrauktas klientas, kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getContractCountOfCustomer($id) {
		$query = "  SELECT COUNT(`sutartys`.`nr`) AS `kiekis`
					FROM `klientai`
						INNER JOIN `sutartys`
							ON `klientai`.`asmens_kodas`=`sutartys`.`fk_klientas`
					WHERE `klientai`.`asmens_kodas`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
}
<?php

/**
 * Darbuotojų redagavimo klasė
 *
 * @author ISK
 */

class employees {
	
	public function __construct() {
		
	}
	
	/**
	 * Darbuotojo išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getEmployee($id) {
		$query = "  SELECT *
					FROM `darbuotojai`
					WHERE `tabelio_nr`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Darbuotojų sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getEmplyeesList($limit = null, $offset = null) {
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "  SELECT *
					FROM `darbuotojai`" . $limitOffsetString;
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Darbuotojų kiekio radimas
	 * @return type
	 */
	public function getEmplyeesListCount() {
		$query = "  SELECT COUNT(`tabelio_nr`) as `kiekis`
					FROM `darbuotojai`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Darbuotojo šalinimas
	 * @param type $id
	 */
	public function deleteEmployee($id) {
		$query = "  DELETE FROM `darbuotojai`
					WHERE `tabelio_nr`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Darbuotojo atnaujinimas
	 * @param type $data
	 */
	public function updateEmployee($data) {
		$query = "  UPDATE `darbuotojai`
					SET    `vardas`='{$data['vardas']}',
						   `pavarde`='{$data['pavarde']}'
					WHERE `tabelio_nr`='{$data['tabelio_nr']}'";
		mysql::query($query);
	}
	
	/**
	 * Darbuotojo įrašymas
	 * @param type $data
	 */
	public function insertEmployee($data) {
		$query = "  INSERT INTO `darbuotojai`
								(
									`tabelio_nr`,
									`vardas`,
									`pavarde`
								) 
								VALUES
								(
									'{$data['tabelio_nr']}',
									'{$data['vardas']}',
									'{$data['pavarde']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Sutarčių, į kurias įtrauktas darbuotojas, kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getContractCountOfEmployee($id) {
		$query = "  SELECT COUNT(`sutartys`.`nr`) AS `kiekis`
					FROM `darbuotojai`
						INNER JOIN `sutartys`
							ON `darbuotojai`.`tabelio_nr`=`sutartys`.`fk_darbuotojas`
					WHERE `darbuotojai`.`tabelio_nr`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
}
<?php

/**
 * Automobilių redagavimo klasė
 *
 * @author ISK
 */

class cars {

	public function __construct() {
		
	}
	
	/**
	 * Automobilio išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getCar($id) {
		$query = "  SELECT `automobiliai`.`id`,
						   `automobiliai`.`valstybinis_nr`,
						   `automobiliai`.`pagaminimo_data`,
						   `automobiliai`.`rida`,
						   `automobiliai`.`radijas`,
						   `automobiliai`.`grotuvas`,
						   `automobiliai`.`kondicionierius`,
						   `automobiliai`.`vietu_skaicius`,
						   `automobiliai`.`registravimo_data`,
						   `automobiliai`.`verte`,
						   `automobiliai`.`pavaru_deze`,
						   `automobiliai`.`degalu_tipas`,
						   `automobiliai`.`kebulas`,
						   `automobiliai`.`bagazo_dydis`,
						   `automobiliai`.`busena`,
						   `automobiliai`.`fk_modelis` AS `modelis`
					FROM `automobiliai`
					WHERE `automobiliai`.`id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Automobilio atnaujinimas
	 * @param type $data
	 */
	public function updateCar($data) {
		$query = "  UPDATE `automobiliai`
					SET    `valstybinis_nr`='{$data['valstybinis_nr']}',
						   `pagaminimo_data`='{$data['pagaminimo_data']}',
						   `rida`='{$data['rida']}',
						   `radijas`='{$data['radijas']}',
						   `grotuvas`='{$data['grotuvas']}',
						   `kondicionierius`='{$data['kondicionierius']}',
						   `vietu_skaicius`='{$data['vietu_skaicius']}',
						   `registravimo_data`='{$data['registravimo_data']}',
						   `verte`='{$data['verte']}',
						   `pavaru_deze`='{$data['pavaru_deze']}',
						   `degalu_tipas`='{$data['degalu_tipas']}',
						   `kebulas`='{$data['kebulas']}',
						   `bagazo_dydis`='{$data['bagazo_dydis']}',
						   `busena`='{$data['busena']}',
						   `fk_modelis`='{$data['modelis']}'
					WHERE `id`='{$data['id']}'";
		mysql::query($query);
	}

	/**
	 * Automobilio įrašymas
	 * @param type $data
	 */
	public function insertCar($data) {
		$query = "  INSERT INTO `automobiliai` 
								(
									`id`,
									`valstybinis_nr`,
									`pagaminimo_data`,
									`rida`,
									`radijas`,
									`grotuvas`,
									`kondicionierius`,
									`vietu_skaicius`,
									`registravimo_data`,
									`verte`,
									`pavaru_deze`,
									`degalu_tipas`,
									`kebulas`,
									`bagazo_dydis`,
									`busena`,
									`fk_modelis`
								) 
								VALUES
								(
									'{$data['id']}',
									'{$data['valstybinis_nr']}',
									'{$data['pagaminimo_data']}',
									'{$data['rida']}',
									'{$data['radijas']}',
									'{$data['grotuvas']}',
									'{$data['kondicionierius']}',
									'{$data['vietu_skaicius']}',
									'{$data['registravimo_data']}',
									'{$data['verte']}',
									'{$data['pavaru_deze']}',
									'{$data['degalu_tipas']}',
									'{$data['kebulas']}',
									'{$data['bagazo_dydis']}',
									'{$data['busena']}',
									'{$data['modelis']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Automobilių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getCarList($limit = null, $offset = null) {
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "  SELECT `automobiliai`.`id`,
						   `automobiliai`.`valstybinis_nr`,
						   `auto_busenos`.`name` AS `busena`,
						   `modeliai`.`pavadinimas` AS `modelis`,
						   `markes`.`pavadinimas` AS `marke`
					FROM `automobiliai`
						LEFT JOIN `modeliai`
							ON `automobiliai`.`fk_modelis`=`modeliai`.`id`
						LEFT JOIN `markes`
							ON `modeliai`.`fk_marke`=`markes`.`id`
						LEFT JOIN `auto_busenos`
							ON `automobiliai`.`busena`=`auto_busenos`.`id`" . $limitOffsetString;
		$data = mysql::select($query);
		
		return $data;
	}

	/**
	 * Automobilių kiekio radimas
	 * @return type
	 */
	public function getCarListCount() {
		$query = "  SELECT COUNT(`automobiliai`.`id`) AS `kiekis`
					FROM `automobiliai`
						LEFT JOIN `modeliai`
							ON `automobiliai`.`fk_modelis`=`modeliai`.`id`
						LEFT JOIN `markes` 
							ON `modeliai`.`fk_marke`=`markes`.`id`
						LEFT JOIN `auto_busenos`
							ON `automobiliai`.`busena`=`auto_busenos`.`id`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Automobilio šalinimas
	 * @param type $id
	 */
	public function deleteCar($id) {
		$query = "  DELETE FROM `automobiliai`
					WHERE `id`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Sutačių, į kurias įtrauktas automobilis, kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getContractCountOfCar($id) {
		$query = "  SELECT COUNT(`sutartys`.`nr`) AS `kiekis`
					FROM `automobiliai`
						INNER JOIN `sutartys`
							ON `automobiliai`.`id`=`sutartys`.`fk_automobilis`
					WHERE `automobiliai`.`id`='{$id}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Didžiausios automobilio id reikšmės radimas
	 * @return type
	 */
	public function getMaxIdOfCar() {
		$query = "  SELECT MAX(`id`) AS `latestId`
					FROM `automobiliai`";
		$data = mysql::select($query);
		
		return $data[0]['latestId'];
	}
	
	/**
	 * Pavarų dėžių sąrašo išrinkimas
	 * @return type
	 */
	public function getGearboxList() {
		$query = "  SELECT *
					FROM `pavaru_dezes`";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Degalų tipo sąrašo išrinkimas
	 * @return type
	 */
	public function getFuelTypeList() {
		$query = "  SELECT *
					FROM `degalu_tipai`";
		$data = mysql::select($query);
		
		return $data;
	}

	/**
	 * Kėbulo tipų sąrašo išrinkimas
	 * @return type
	 */
	public function getBodyTypeList() {
		$query = "  SELECT *
					FROM `kebulu_tipai`";
		$data = mysql::select($query);
		
		return $data;
	}

	/**
	 * Bagažo tipų sąrašo išrinkimas
	 * @return type
	 */
	public function getLugageTypeList() {
		$query = "  SELECT *
					FROM `lagaminai`";
		$data = mysql::select($query);
		
		return $data;
	}

	/**
	 * Automobilio būsenų sąrašo išrinkimas
	 * @return type
	 */
	public function getCarStateList() {
		$query = "  SELECT *
					FROM `auto_busenos`";
		$data = mysql::select($query);
		
		return $data;
	}
	
}
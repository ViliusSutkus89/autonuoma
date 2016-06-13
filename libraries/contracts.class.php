<?php

/**
 * Sutarčių redagavimo klasė
 *
 * @author ISK
 */

class contracts {

	public function __construct() {
		
	}
	
	/**
	 * Sutarčių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getContractList($limit, $offset) {
		$query = "  SELECT `sutartys`.`nr`,
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
							ON `sutartys`.`busena`=`sutarties_busenos`.`id` LIMIT {$limit} OFFSET {$offset}";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Sutarčių kiekio radimas
	 * @return type
	 */
	public function getContractListCount() {
		$query = "  SELECT COUNT(`sutartys`.`nr`) AS `kiekis`
					FROM `sutartys`
						LEFT JOIN `darbuotojai`
							ON `sutartys`.`fk_darbuotojas`=`darbuotojai`.`tabelio_nr`
						LEFT JOIN `klientai`
							ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
						LEFT JOIN `sutarties_busenos`
							ON `sutartys`.`busena`=`sutarties_busenos`.`id`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Sutarties išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getContract($id) {
		$query = "  SELECT `sutartys`.`nr`,
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
						   (IFNULL(SUM(`uzsakytos_paslaugos`.`kaina` * `uzsakytos_paslaugos`.`kiekis`), 0) + `sutartys`.`kaina`) AS `bendra_kaina`
					FROM `sutartys`
						LEFT JOIN `uzsakytos_paslaugos`
							ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
					WHERE `sutartys`.`nr`='{$id}' GROUP BY `sutartys`.`nr`";
		$data = mysql::select($query);
		
		return $data[0];
	}
	
	/**
	 * Užsakytų papildomų paslaugų sąrašo išrinkimas
	 * @param type $orderId
	 * @return type
	 */
	public function getOrderedServices($orderId) {
		$query = "  SELECT *
					FROM `uzsakytos_paslaugos`
					WHERE `fk_sutartis`='{$orderId}'";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Sutarties atnaujinimas
	 * @param type $data
	 */
	public function updateContract($data) {
		$query = "  UPDATE `sutartys`
					SET    `sutarties_data`='{$data['sutarties_data']}',
						   `nuomos_data_laikas`='{$data['nuomos_data_laikas']}',
						   `planuojama_grazinimo_data_laikas`='{$data['planuojama_grazinimo_data_laikas']}',
						   `faktine_grazinimo_data_laikas`='{$data['faktine_grazinimo_data_laikas']}',
						   `pradine_rida`='{$data['pradine_rida']}',
						   `galine_rida`='{$data['galine_rida']}',
						   `kaina`='{$data['kaina']}',
						   `degalu_kiekis_paimant`='{$data['degalu_kiekis_paimant']}',
						   `dagalu_kiekis_grazinus`='{$data['dagalu_kiekis_grazinus']}',
						   `busena`='{$data['busena']}',
						   `fk_klientas`='{$data['fk_klientas']}',
						   `fk_darbuotojas`='{$data['fk_darbuotojas']}',
						   `fk_automobilis`='{$data['fk_automobilis']}',
						   `fk_grazinimo_vieta`='{$data['fk_grazinimo_vieta']}',
						   `fk_paemimo_vieta`='{$data['fk_paemimo_vieta']}'
					WHERE `nr`='{$data['nr']}'";
		mysql::query($query);
	}
	
	/**
	 * Sutarties įrašymas
	 * @param type $data
	 */
	public function insertContract($data) {
		$query = "  INSERT INTO `sutartys`
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
								)
								VALUES
								(
									'{$data['nr']}',
									'{$data['sutarties_data']}',
									'{$data['nuomos_data_laikas']}',
									'{$data['planuojama_grazinimo_data_laikas']}',
									'{$data['faktine_grazinimo_data_laikas']}',
									'{$data['pradine_rida']}',
									'{$data['galine_rida']}',
									'{$data['kaina']}',
									'{$data['degalu_kiekis_paimant']}',
									'{$data['dagalu_kiekis_grazinus']}',
									'{$data['busena']}',
									'{$data['fk_klientas']}',
									'{$data['fk_darbuotojas']}',
									'{$data['fk_automobilis']}',
									'{$data['fk_grazinimo_vieta']}',
									'{$data['fk_paemimo_vieta']}'
								)";
		mysql::query($query);
	}
	
	/**
	 * Sutarties šalinimas
	 * @param type $id
	 */
	public function deleteContract($id) {
		$query = "  DELETE FROM `sutartys`
					WHERE `nr`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Užsakytų papildomų paslaugų šalinimas
	 * @param type $contractId
	 */
	public function deleteOrderedServices($contractId) {
		$query = "  DELETE FROM `uzsakytos_paslaugos`
					WHERE `fk_sutartis`='{$contractId}'";
		mysql::query($query);
	}
	
	/**
	 * Užsakytų papildomų paslaugų atnaujinimas
	 * @param type $data
	 */
	public function updateOrderedServices($data) {
		$this->deleteOrderedServices($data['nr']);
		
		foreach($data['paslaugos'] as $key=>$val) {
			$tmp = explode(":", $val);
			$serviceId = $tmp[0];
			$price = $tmp[1];
			$date_from = $tmp[2];
			$query = "  INSERT INTO `uzsakytos_paslaugos`
									(
										`fk_sutartis`,
										`fk_kaina_galioja_nuo`,
										`fk_paslauga`,
										`kiekis`,
										`kaina`
									)
									VALUES
									(
										'{$data['nr']}',
										'{$date_from}',
										'{$serviceId}',
										'{$data['kiekiai'][$key]}',
										'{$price}'
									)";
				mysql::query($query);
		}
	}
	
	/**
	 * Sutarties būsenų sąrašo išrinkimas
	 * @return type
	 */
	public function getContractStates() {
		$query = "  SELECT *
					FROM `sutarties_busenos`";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Aikštelių sąrašo išrinkimas
	 * @return type
	 */
	public function getParkingLots() {
		$query = "  SELECT *
					FROM `aiksteles`";
		$data = mysql::select($query);
		
		return $data;
	}
	
	/**
	 * Paslaugos kainų įtraukimo į užsakymus kiekio radimas
	 * @param type $serviceId
	 * @param type $validFrom
	 * @return type
	 */
	public function getPricesCountOfOrderedServices($serviceId, $validFrom) {
		$query = "  SELECT COUNT(`uzsakytos_paslaugos`.`fk_paslauga`) AS `kiekis`
					FROM `paslaugu_kainos`
						INNER JOIN `uzsakytos_paslaugos`
							ON `paslaugu_kainos`.`fk_paslauga`=`uzsakytos_paslaugos`.`fk_paslauga` AND `paslaugu_kainos`.`galioja_nuo`=`uzsakytos_paslaugos`.`fk_kaina_galioja_nuo`
					WHERE `paslaugu_kainos`.`fk_paslauga`='{$serviceId}' AND `paslaugu_kainos`.`galioja_nuo`='{$validFrom}'";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}

	public function getCustomerContracts($dateFrom, $dateTo) {
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " WHERE `sutartys`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " WHERE `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "  SELECT  `sutartys`.`nr`,
							`sutartys`.`sutarties_data`,
							`klientai`.`asmens_kodas`,
							`klientai`.`vardas`,
						    `klientai`.`pavarde`,
						    `sutartys`.`kaina` as `sutarties_kaina`,
						    IFNULL(sum(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`), 0) as `sutarties_paslaugu_kaina`,
						    `t`.`bendra_kliento_sutarciu_kaina`,
						    `s`.`bendra_kliento_paslaugu_kaina`
					FROM `sutartys`
						INNER JOIN `klientai`
							ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
						LEFT JOIN `uzsakytos_paslaugos`
							ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
						LEFT JOIN (
							SELECT `asmens_kodas`,
									sum(`sutartys`.`kaina`) AS `bendra_kliento_sutarciu_kaina`
							FROM `sutartys`
								INNER JOIN `klientai`
									ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
							{$whereClauseString}
							GROUP BY `asmens_kodas`
						) `t` ON `t`.`asmens_kodas`=`klientai`.`asmens_kodas`
						LEFT JOIN (
							SELECT `asmens_kodas`,
									IFNULL(sum(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`), 0) as `bendra_kliento_paslaugu_kaina`
							FROM `sutartys`
								INNER JOIN `klientai`
									ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
								LEFT JOIN `uzsakytos_paslaugos`
									ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
								{$whereClauseString}							
								GROUP BY `asmens_kodas`
						) `s` ON `s`.`asmens_kodas`=`klientai`.`asmens_kodas`
					{$whereClauseString}
					GROUP BY `sutartys`.`nr` ORDER BY `klientai`.`pavarde` ASC";
		$data = mysql::select($query);

		return $data;
	}
	
	public function getSumPriceOfContracts($dateFrom, $dateTo) {
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " WHERE `sutartys`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " WHERE `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "  SELECT sum(`sutartys`.`kaina`) AS `nuomos_suma`
					FROM `sutartys`
					{$whereClauseString}";
		$data = mysql::select($query);

		return $data;
	}

	public function getSumPriceOfOrderedServices($dateFrom, $dateTo) {
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " WHERE `sutartys`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " WHERE `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "  SELECT sum(`uzsakytos_paslaugos`.`kiekis` * `uzsakytos_paslaugos`.`kaina`) AS `paslaugu_suma`
					FROM `sutartys`
						INNER JOIN `uzsakytos_paslaugos`
							ON `sutartys`.`nr`=`uzsakytos_paslaugos`.`fk_sutartis`
					{$whereClauseString}";
		$data = mysql::select($query);

		return $data;
	}
	
	public function getDelayedCars($dateFrom, $dateTo) {
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " AND `sutartys`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `sutartys`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "  SELECT `nr`,
						   `sutarties_data`,
						   `planuojama_grazinimo_data_laikas`,
						   IF(`faktine_grazinimo_data_laikas`='0000-00-00 00:00:00', 'negrąžinta', `faktine_grazinimo_data_laikas`) AS `grazinta`,
						   `klientai`.`vardas`,
						   `klientai`.`pavarde`
					FROM `sutartys`
						INNER JOIN `klientai`
							ON `sutartys`.`fk_klientas`=`klientai`.`asmens_kodas`
					WHERE (DATEDIFF(`faktine_grazinimo_data_laikas`, `planuojama_grazinimo_data_laikas`) >= 1 OR
						(`faktine_grazinimo_data_laikas` = '0000-00-00 00:00:00' AND DATEDIFF(NOW(), `planuojama_grazinimo_data_laikas`) >= 1))
					{$whereClauseString}";
		$data = mysql::select($query);

		return $data;
	}
	
}
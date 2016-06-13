<?php

/**
 * Sąrašo puslapių kūrimo klasė
 *
 * @author ISK
 */

class paging {
	private $pageRange;
	
	public $first;
	public $size;
	
	public $totalRecords;
	public $totalPages;
	
	public $data = array();
	
	/**
	* @desc Konstruktorius
	* @param int kiek įrašų rodoma viename puslapyje
	*/
	public function __construct($rowsPerPage) {
		$this->size = $rowsPerPage;
		$this->pageRange = 5;
	}
	
	/**
	* @desc Puslapių sudarymas
	* @param int iš viso įrašų sąraše
	* @param int pasirinktas puslapis
	*/
	public function process($total, $currentPage) {
		// suskaičiuojame puslapių kiekį
		$pageCount = ceil($total / $this->size);
		
		// sudarome statistiką
		$this->totalRecords = (int) $total;
		$this->totalPages = (int) ($pageCount) ? $pageCount : 1;
		$this->first = ($currentPage - 1) * $this->size;
		
		// suformuojame puslapius
		for($i = 1; $i <= $pageCount; $i++) {
			$row['isActive'] = ($i == $currentPage) ? 1 : 0;
			$row['page'] = $i;
			
			$this->data[] = $row;
		}		
	}
}

?>
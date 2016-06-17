<?php
/**
 * Pork Formvalidator. validates fields by regexes and can sanatize them. Uses PHP filter_var built-in functions and extra regexes 
 * @package pork
 */


/**
 * Pork.FormValidator
 * Validates arrays or properties by setting up simple arrays
 * 
 * @package pork
 * @author SchizoDuckie
 * @copyright SchizoDuckie 2009
 * @version 1.0
 * @access public
 */
class validator
{
    public $regexes = Array(
		'date' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$", // 2016-01-15
		'datetime' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}(:[0-9]{1,2})?\$", // 2016-01-15 12:12, 2016-01-15 12:12:00
		'positivenumber' => "^[0-9\.]+\$", // teigiami sveikieji skaičiai bei skaičiai su kableliu (pvz.: 25.14)
		'price' => "^([1-9][0-9]*|0)(\.[0-9]{2})?\$", // kaina (pvz.: 25.99)
		'anything' => "^[\d\D]{1,}\$", // bet koks simbolis
		'alfanum' => "^[0-9a-zA-ZąčęėįšųūžĄČĘĖĮŠŲŪŽ ,.-_\\s\?\!]+\$", // tekstas
		'not_empty' => "[a-z0-9A-ZąčęėįšųūžĄČĘĖĮŠŲŪŽ]+", // bet kokia reikšmė, kuri prasideda raide arba skaitmeniu
		'words' => "^[A-Za-ząčęėįšųūžĄČĘĖĮŠŲŪŽ]+[A-Za-ząčęėįšųūžĄČĘĖĮŠŲŪŽ \\s]*\$", // žodžiai
		'phone' => "^[0-9]{9,14}\$" // telefonas (pvz.: 860000000)
		/* BE ŠIŲ FORMATŲ DAR GALIMA NAUDOTI STANDARTINIUS:
		 * email,
		 * int,
		 * float,
		 * boolen,
		 * ip,
		 * url*/
    );
	
    private $validations, $mandatories, $lengths, $errors, $corrects, $fields;

	/**
	 * Konstruktorius
	 * @param type $validations
	 * @param type $mandatories
	 */
    public function __construct($validations = array(), $mandatories = array(), $lengths = array()) {
    	$this->validations = $validations;
    	$this->mandatories = $mandatories;
		$this->lengths = $lengths;
    	$this->errors = array();
    	$this->corrects = array();
    }

    /**
	 * Patikrinamas reikšių masyvas
	 * @param type $items
	 * @return type
	 */
    public function validate($items) {
    	$this->fields = $items;
    	$havefailures = false;
    	foreach($items as $key=>$val) {
			if(((!is_array($val) && strlen($val) == 0) || key_exists($key, $this->validations) === false) && array_search($key, $this->mandatories) === false) {
				$this->corrects[] = $key;
				continue;
			}

			$result = false;
			if(is_array($val)) {
				$result = $this->validateArray($val, $key);
			} else {
				$result = $this->validateItem($val, $this->validations[$key]);
			}

			if($result === true) {
				if(key_exists($key, $this->lengths)) {
					if(strlen($val) > $this->lengths[$key]) {
						$result = false;
					}
				}
			}

			if($result === false) {
				$havefailures = true;
				$this->addError($key, $this->validations[$key]);
			} else {
				$this->corrects[] = $key;
			}
    	}

    	return(!$havefailures);
    }
	
	private function validateArray($array, $key) {
		$havefailures = false;
		if((key_exists($key, $this->validations) === false) && array_search($key, $this->mandatories) === false) {
			$this->corrects[] = $key;
			return false;
		}
		
		foreach($array as $item) {
			$result = false;
			if($item == "" && array_search($key, $this->mandatories) === false) {
				$result = true;
			} else {
				$result = $this->validateItem($item, $this->validations[$key]);
			}

			if($result === false) {
				$havefailures = true;
				$this->addError($key, $this->validations[$key]);
			}
		}
		
		if($havefailures == false) {
			$this->corrects[] = $key;
		}
		
		return !$havefailures;
	}
	
    /**
	 * Gaunamas klaidos pranešimas
	 * @return type
	 */
    public function getErrorHTML() {
    	if(!empty($this->errors)) {
    		$errors = array();
    		foreach($this->errors as $key=>$val) {
				$errors[] = "<li>" . $key . "</li>";
			}
    		$output = "<ul>" . implode('', $errors) . "</ul>";
    	}
    	
    	return($output);
    }

	/**
	 * Į klaidų masyvą įtraukiama klaida
	 * @param type $field
	 * @param type $type
	 */
    private function addError($field, $type='string') {
    	$this->errors[$field] = $type;
    }

    /**
	 * Pagal nurodytą tipą patikrinama viena reikšmė
	 * @param type $var
	 * @param type $type
	 * @return type
	 */
    public function validateItem($var, $type) {
		if(array_key_exists($type, $this->regexes)) {
    		$returnval =  filter_var($var, FILTER_VALIDATE_REGEXP, array("options"=> array("regexp"=>'!'.$this->regexes[$type].'!i'))) !== false;
    		return($returnval);
    	}
    	$filter = false;
    	switch($type) {
    		case 'email':
    			$var = substr($var, 0, 254);
    			$filter = FILTER_VALIDATE_EMAIL;	
    		break;
    		case 'int':
    			$filter = FILTER_VALIDATE_INT;
    		break;
    		case 'float':
    			$filter = FILTER_VALIDATE_FLOAT;
    		break;
    		case 'boolean':
    			$filter = FILTER_VALIDATE_BOOLEAN;
    		break;
    		case 'ip':
    			$filter = FILTER_VALIDATE_IP;
    		break;
    		case 'url':
    			$filter = FILTER_VALIDATE_URL;
    		break;
    	}
    	return ($filter === false) ? false : filter_var($var, $filter) !== false ? true : false;
    }

	function preparePostFieldsForSQL() {
		$data = array();

		foreach($this->fields as $key=>$val) {
			$tmp = null;
			if(!is_array($val)) {
				$tmp = $this->html_escape($val);
			} else {
				foreach($val as $key2 => $val2) {
					$tmp[] = $this->html_escape($val2);
				}
			}
			
			if(!in_array($key, $this->mandatories) && ($tmp == '' || $tmp == array())) {
				$data[$key] = '';
			} else {
				if(!is_array($tmp)) {
					$data[$key] = $tmp;
				} else {
					$data[$key] = $tmp;
				}
				
			}
		}

    return $data;
	}

    /*
     * Escape HTML entities, to prevent XSS
     */
  function html_escape($input) {
    $data = str_replace('&', '&amp;', $input);
    $data = str_replace(
      array('<', '>', '"', "'", "/"),
      array('&lt;', '&gt;', '&quot;', '&#x27;', '&#x2f;'),
      $data
    );

    return $data;
  }
	
}


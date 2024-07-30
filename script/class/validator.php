<?php
/* Initialization */
require_once("Debugger.php");
require_once("init.php");

class validator {
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->set_log_level(4);
	}

	public function check_sku($sku) {
		if (preg_match('/^[A-Z0-9a-z\-+x\/\._\& ]+$/', $sku)) {
			$this->output->info("SKU: $sku - Valid SKU!");
			return true;
		} else {
			$this->output->info("SKU: $sku - Invalid SKU!");
			return false;
		}
	}

	public function check_asin($asin) {
		if (preg_match('/^[A-Z0-9]{10}$/', $asin)) {
			$this->output->info("ASIN: $asin - Valid ASIN!");
			return true;
		} else {
			$this->output->info("ASIN: $asin - Invalid ASIN!");
			return false;
		}
	}

	public function check_upc($upc) {
		if (preg_match('/^[0-9]{12,13}$/', $upc)) {
			$this->output->info("UPC: $upc - Valid UPC!");
			return true;
		} else {
			$this->output->info("UPC: $upc - Invalid UPC!");
			return false;
		}
	}

	public function check_url($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
			$check = @fopen($url, "r");
			if ($check) {
				$this->output->info("URL: $url - Valid link!");
				return true;
			} else {
				$this->output->info("URL: $url - Not a valid link!");
				return false;
			}
		} else {
			$this->output->error("URL: $url is not a valid URL in format!");
			return false;
		}
	}
}
?>

<?php
/* Initialization */
require_once("debugger.php");

class validator {
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function check_sku($sku) {
		if (preg_match('/^[A-Z]+-[A-Z0-9-+x. \/]+$/', $sku)) {
			return true;
		} else {
			return false;
		}
	}

	public function check_asin($asin) {
		if (preg_match('/^[A-Z0-9]{10}$/', $asin)) {
			return true;
		} else {
			return false;
		}
	}

	public function check_url($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
			$check = @fopen($url, "r");
			if ($check) {
				return true;
			} else {
				return false;
			}
		} else {
			$this->output->error("URL: $url is not a valid URL in format!");
			return false;
		}
	}
}
?>

<?php
/* Initialization */
require_once("debugger.php");

class validator {
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function check_sku($sku) {
		if (preg_match('/^[A-Z]+-[A-Z0-9-+x. ]+$/', $sku)) {
			$this->output->notice("SKU: $sku is valid!");
			return true;
		} else {
			$this->output->warning("SKU: $sku is not valid!");
			return false;
		}
	}

	public function check_url($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
			$check = @fopen($url, "r");
			if ($check) {
				$this->output->notice("URL: $url is valid!");
				return true;
			} else {
				$this->output->error("URL: $url is not valid!");
				return false;
			}
		} else {
			$this->output->error("URL: $url is not a valid URL!");
			return false;
		}
	}
}
?>

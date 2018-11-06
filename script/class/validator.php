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
}
?>

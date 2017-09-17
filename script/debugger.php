<?php
class debugger {
	// Debug: 0 - False, 1 - True
	private $debug = 0;

	public function info($message) {
		if ($this->debug) {
			echo "[Info] $message\n";
		} 
	}

	public function error($message) {
		if ($this->debug) {
			echo "[Error] $message\n";
		}
	}
}
?>

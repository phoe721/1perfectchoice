<?php
class debugger {
	// Debug: 0 - False, 1 - True
	private $debug = 0;

	public function info($message) {
		if ($this->debug) {
			echo "[Info] $message\n";
		} 
	}

	public function notice($message) {
		if ($this->debug) {
			echo "[Notice] $message\n";
		} 
	}

	public function warning($message) {
		if ($this->debug) {
			echo "[Warning] $message\n";
		}
	}

	public function error($message) {
		if ($this->debug) {
			echo "[Error] $message\n";
		}
	}

	public function debug_on() {
		$this->debug = 1;
	}

	public function debug_off() {
		$this->debug = 0;
	}
}
?>

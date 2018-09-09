<?php
class debugger {
	private $debug = true;

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
		$this->debug = true;
	}

	public function debug_off() {
		$this->debug = false;
	}
}
?>

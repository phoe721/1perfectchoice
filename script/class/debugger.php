<?php
require_once(__DIR__ . "/../init.php");

class debugger {
	private $debug = false;

	public function info($message) {
		if ($this->debug) echo "[Info] $message\n";
		$this->logger("[Info] $message");
	}

	public function notice($message) {
		if ($this->debug) echo "[Notice] $message\n";
		$this->logger("[Notice] $message");
	}

	public function warning($message) {
		if ($this->debug) echo "[Warning] $message\n";
		$this->logger("[Warning] $message");
	}

	public function error($message) {
		if ($this->debug) echo "[Error] $message\n";
		$this->logger("[Error] $message");
	}

	public function debug_on() {
		$this->debug = true;
	}

	public function debug_off() {
		$this->debug = false;
	}

	public function logger($msg) {
		$timestring = date('Y-m-d H:i:s', strtotime('now'));
		$msg = "$timestring $msg\n";
		$file = fopen(LOG_FILE, 'a+');
		if ($file) fwrite($file, $msg);
		fclose($file);
	}
}
?>

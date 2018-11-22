<?php
// loglevel: 0 = info, 1 = notice, 2 = warning, 3 = error 
require_once(__DIR__ . "/../init.php");

class debugger {
	private $loglevel = 0;
	private $statusFile;

	public function info($message) {
		if ($this->loglevel == 0) echo $message . PHP_EOL;
		$this->logger("[Info] $message");
	}

	public function notice($message) {
		$this->logger("[Notice] $message");
	}

	public function warning($message) {
		$this->logger("[Warning] $message");
	}

	public function error($message) {
		$this->logger("[Error] $message");
	}

	public function set_log_level($level) {
		$this->loglevel = $level;
	}

	public function set_status_file($path) {
		$this->statusFile = $path;
	}

	public function logger($msg) {
		$timestring = date('Y-m-d H:i:s', strtotime('now'));
		$msg = "$timestring $msg" . PHP_EOL;
		$file = fopen(LOG_FILE, 'a+');
		if ($file) fwrite($file, $msg);
		fclose($file);
	}

	public function log_status($msg) {
		$msg .= PHP_EOL;
		$file = fopen($this->statusFile, "w");
		if ($file) fwrite($file, $msg);
		fclose($file);
	}
}
?>

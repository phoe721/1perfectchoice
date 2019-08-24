<?php
// loglevel: 0 = info, 1 = notice, 2 = warning, 3 = error 
require_once(__DIR__ . "/../init.php");

class debugger {
	private $loglevel = 4;

	public function info($message) {
		if ($this->loglevel >= 0) $this->logger("[Info][" . basename($_SERVER['PHP_SELF']) . "] $message");
	}

	public function notice($message) {
		if ($this->loglevel >= 1) $this->logger("[Notice][" . basename($_SERVER['PHP_SELF']) . "] $message");
	}

	public function error($message) {
		if ($this->loglevel >= 2) $this->logger("[Error][" . basename($_SERVER['PHP_SELF']) . "] $message");
	}

	public function warning($message) {
		if ($this->loglevel >= 3) $this->logger("[Warning][" . basename($_SERVER['PHP_SELF']) . "] $message");
	}

	public function set_log_level($level) {
		$this->loglevel = $level;
	}

	public function logger($msg) {
		$timestring = date('Y-m-d H:i:s', strtotime('now'));
		$msg = "$timestring $msg" . PHP_EOL;
		$file = fopen(LOG_FILE, 'a+');
		if ($file) fwrite($file, $msg);
		fclose($file);
	}
}
?>

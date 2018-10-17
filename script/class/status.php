<?php
class status {
	private $file;

	public function set_file($path) {
		$this->file = $path;
	}

	public function log_status($msg) {
		$msg .= PHP_EOL;
		$file = fopen($this->file, "w");
		if ($file) fwrite($file, $msg);
		fclose($file);
	}
}
?>

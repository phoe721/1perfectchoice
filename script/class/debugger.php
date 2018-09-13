<?php
require_once('database.php');

class debugger {
	private $db;
	private $category = "general";
	private $debug = false;

	public function __construct() {
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

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

	public function set_category($category) {
		$this->category = $category;
	}

	public function logger($msg) {
		$timestring = date('Y-m-d H:i:s', strtotime('now'));
		$result = $this->db->query("INSERT INTO log (id, category, message, datetime) VALUES ('', '$this->category', '$msg', '$timestring')");
	}

	public function debug_on() {
		$this->debug = true;
	}

	public function debug_off() {
		$this->debug = false;
	}

}
?>

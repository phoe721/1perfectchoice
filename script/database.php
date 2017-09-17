<?php
require_once("debugger.php");

class database {
	private $con;
	private $result;
	private $output;

	public function __construct() {
		$this->output = new debugger;
	}

	public function __destruct() {
		$this->output->info("Close MySQL connection");
		mysqli_close($this->con);
	}

	public function connect($remoteHost, $username, $password, $database) {
		$this->con = mysqli_connect($remoteHost, $username, $password, $database);
		if (mysqli_connect_errno()) {
			$this->output->error("Failed to connect to MySQL server: " . mysqli_connect_error());
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		$this->output->info("Connecting to MySQL server");
		return $this->con;
	}

	public function query($query) {
		$this->result = mysqli_query($this->con, $query);
		if (!$this->result) {
			$this->output->error("Query to MySQL server failed: " . $this->error());
			return false;
		} 
		$this->output->info("Query to MySQL server successfully");
		return $this->result;
	}

	public function getConnection() {
		$this->output->info("Returning MySQL connection");
		return $this->con;
	}

	public function last_insert_id() {
		$this->output->info("Returning last insert ID");
		return mysqli_insert_id($this->con); 
	}

	public function get_info() {
		$this->output->info("Getting info");
		return mysqli_info($this->con);
	}

	public function error() {
		$this->output->info("Returning MySQL error message");
		return mysqli_errno($this->con) . ": " . mysqli_error($this->con);
	}
}
?>

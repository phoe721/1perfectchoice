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
		if ($this->con) {
			$this->output->info("Close DB connection");
			mysqli_close($this->con);
		} else {
			$this->output->notice("DB connection was not made");
		}
	}

	public function connect($remoteHost, $username, $password, $database) {
		$this->con = mysqli_connect($remoteHost, $username, $password, $database);
		if (mysqli_connect_errno()) {
			$this->output->error("Failed to connect to DB server: " . mysqli_connect_error());
			return $this->con;
		} else {
			$this->output->info("Connected to DB server");
			return false;
		}
	}

	public function query($query) {
		if ($this->con) {
			$this->result = mysqli_query($this->con, $query);
			if (!$this->result) {
				$this->output->error("Query to DB server failed: " . $this->error());
			} else { 
				$this->output->info("Query to DB server successfully");
				return $this->result;
			}
		} else {
			$this->output->notice("DB connection was not made");
		}

		return false;
	}

	public function getConnection() {
		if ($this->con) {
			$this->output->info("Returning DB connection");
			return $this->con;
		} else {
			$this->output->notice("DB connection was not made");
			return false;
		}
	}

	public function last_insert_id() {
		if ($this->con) {
			$this->output->info("Returning last insert ID");
			return mysqli_insert_id($this->con); 
		} else {
			$this->output->notice("DB connection was not made");
			return false;
		}
	}

	public function get_info() {
		if ($this->con) {
			$this->output->info("Getting info");
			return mysqli_info($this->con);
		} else {
			$this->output->notice("DB connection was not made");
			return false;
		}
	}

	public function error() {
		if ($this->con) {
			$this->output->info("Returning DB error message");
			return mysqli_errno($this->con) . ": " . mysqli_error($this->con);
		} else {
			$this->output->notice("DB connection was not made");
			return false;
		}
	}

	public function debug_on() {
		$this->output->debug_on();
	}

	public function debug_off() {
		$this->output->debug_off();
	}
}
?>

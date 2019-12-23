<?php
require_once("debugger.php");

class database {
	private static $instance = null;
	private $con;
	private $result;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->con = $this->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->con, "utf8");
	}

	public function __destruct() {
		if ($this->con) {
			$this->output->notice("Close DB connection!");
			mysqli_close($this->con);
		} else {
			$this->output->error("DB connection was not made!");
		}
	}

	public function connect($remoteHost, $username, $password, $database) {
		$this->con = mysqli_connect($remoteHost, $username, $password, $database);
		if (mysqli_connect_errno()) {
			$this->output->error("Failed to connect to DB server: " . mysqli_connect_error());
			return false;
		} else {
			$this->output->notice("Connected to DB server!");
			return $this->con;
		}
	}

	public function query($query) {
		if ($this->con) {
			$this->result = mysqli_query($this->con, $query);
			if (!$this->result) {
				$this->output->error("Query to DB server failed: " . $this->error());
			} else { 
				$this->output->notice("Query to DB server successfully!");
				return $this->result;
			}
		} else {
			$this->output->error("DB connection was not made!");
		}

		return false;
	}

	public function real_escape_string($string) {
		if ($this->con) {
			$this->result = mysqli_real_escape_string($this->con, $string);
			if (!$this->result) {
				$this->output->error("Query to DB server failed: " . $this->error());
			} else { 
				$this->output->notice("Query to DB server successfully!");
				return $this->result;
			}
		} else {
			$this->output->error("DB connection was not made!");
		}

		return false;
	}

	public function getConnection() {
		if ($this->con) {
			$this->output->notice("Returning DB connection!");
			return $this->con;
		} else {
			$this->output->error("DB connection was not made!");
			return false;
		}
	}

	public static function getInstance() {
		if (!self::$instance) self::$instance = new database;
		return self::$instance;
	}

	public function last_insert_id() {
		if ($this->con) {
			$this->output->notice("Returning last insert ID!");
			return mysqli_insert_id($this->con); 
		} else {
			$this->output->error("DB connection was not made!");
			return false;
		}
	}

	public function get_info() {
		if ($this->con) {
			$this->output->notice("Getting info!");
			return mysqli_info($this->con);
		} else {
			$this->output->error("DB connection was not made!");
			return false;
		}
	}

	public function error() {
		if ($this->con) {
			$this->output->notice("Returning DB error message!");
			return mysqli_errno($this->con) . ": " . mysqli_error($this->con);
		} else {
			$this->output->error("DB connection was not made!");
			return false;
		}
	}
}
?>

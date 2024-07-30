<?php
require_once("Debugger.php");
/**
 * Database Class for handling database operations
 */
class Database {
    private static $instance = null;
    private $con = null;
    private $result = null;
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'c7w2l181';
    private $name = '1perfectchoice';

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
		$this->output = new debugger;
        $this->connect();
    }

    /**
     * Establishes a connection to the database
     */
    private function connect() {
        $this->con = mysqli_connect($this->host, $this->user, $this->pass, $this->name);

        if (!$this->con) {
            $this->handleError("Connection to DB server failed");
        }
    }

    /**
     * Executes a query on the database
     * 
     * @param string $query
     * @return mixed|bool Query result or false on failure
     */
    public function query($query) {
        if ($this->con) {
            $this->result = mysqli_query($this->con, $query);
            if (!$this->result) {
                $this->handleError("Query to DB server failed");
                return false;
            }
            return $this->result;
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Escapes a string for safe usage in a query
     * 
     * @param string $string
     * @return string|bool Escaped string or false on failure
     */
    public function real_escape_string($string) {
        if ($this->con) {
            return mysqli_real_escape_string($this->con, $string);
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Returns the database connection
     * 
     * @return mysqli|bool Database connection or false if not connected
     */
    public function getConnection() {
        if ($this->con) {
            return $this->con;
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Returns the singleton instance of the Database class
     * 
     * @return Database
     */
    public static function getInstance() {
        if (!self::$instance) self::$instance = new Database();
        return self::$instance;
    }

    /**
     * Returns the ID of the last inserted row
     * 
     * @return int|bool Last insert ID or false on failure
     */
    public function last_insert_id() {
        if ($this->con) {
            return mysqli_insert_id($this->con);
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Returns information about the most recent query
     * 
     * @return string|bool Information or false on failure
     */
    public function get_info() {
        if ($this->con) {
            return mysqli_info($this->con);
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Returns the error message for the last MySQLi operation
     * 
     * @return string|bool Error message or false on failure
     */
    public function error() {
        if ($this->con) {
            return mysqli_errno($this->con) . ": " . mysqli_error($this->con);
        }
        $this->handleError("DB connection was not made");
        return false;
    }

    /**
     * Handles error logging
     * 
     * @param string $message
     */
    private function handleError($message) {
        error_log($message);
		$this->output->error($message);
    }
}
?>

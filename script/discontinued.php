<?
/* Initialization */
require_once("init.php");
require_once("database.php");

class discontinued {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert_discontinued_product($code, $item_no) {
		$result = $this->db->query("INSERT INTO discontinued (did, code, item_no) VALUES ('', '$code', '$item_no')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code has been inserted to discontinued table successfully!");
		} else {
			$this->output->error("Failed to insert $item_no to discontinued table!");
		}
	}
	
	public function truncate_discontinued_table() {
		$result = $this->db->query("TRUNCATE TABLE discontinued");
		if ($result) {
			$this->output->info("Discontinued table truncated!");
		} else {
			$this->output->error("Failed to truncate discontinued table!");
		}
	}
	
	public function update_discontinued_table_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE discontinued");
		if ($result) {
			$this->output->info("Discontinued table updated with $filePath!");
		} else {
			$this->output->error("Failed to update discontinued table with $filePath!");
		}
	}
	
	public function discontinued_table_record_count() {
		$result = $this->db->query("SELECT COUNT(*) FROM discontinued");
		return mysqli_num_rows($result);
	}
}

?>

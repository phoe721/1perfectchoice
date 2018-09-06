<?
/* Initialization */
require_once("init.php");
require_once("database.php");

class discontinued {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no) {
		$result = $this->db->query("INSERT INTO discontinued (did, code, item_no) VALUES ('', '$code', '$item_no')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code has been inserted successfully!");
		} else {
			$this->output->error("Failed to insert $item_no!");
		}
	}
	
	public function check($code, $item_no) {
		$result = $this->db->query("SELECT * FROM discontinued WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code is discontinued!");
		} else {
			$this->output->info("Item: $item_no, Code: $code is still active!");
		}
	}

	public function truncate_table() {
		$result = $this->db->query("TRUNCATE TABLE discontinued");
		if ($result) {
			$this->output->info("Table truncated!");
		} else {
			$this->output->error("Failed to truncate!");
		}
	}
	
	public function update_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE discontinued");
		if ($result) {
			$this->output->info("Table updated with $filePath!");
		} else {
			$this->output->error("Failed to update table with $filePath!");
		}
	}
	
	public function get_record_count() {
		$result = $this->db->query("SELECT * FROM discontinued");
		return mysqli_num_rows($result);
	}
}

?>

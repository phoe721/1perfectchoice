<?
/* Initialization */
require_once("init.php");
require_once("debugger.php");
require_once("database.php");

class product_discontinued {
	private $db;
	private $con;
	private $result;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->con = $this->db->connect("localhost", "root", "c7w2l181", "1perfectchoice");
		mysqli_set_charset($this->con, "utf8");
	}

	public function insert_discontinued_product($code, $item_no) {
		$this->result = $this->db->query("INSERT INTO product_discontinued (did, code, item_no) VALUES ('', '$code', '$item_no')");
		if ($this->result) {
			$this->output->info("Item: $item_no, Code: $code has been inserted to product_discontinued table successfully!");
		} else {
			$this->output->error("Failed to insert $item_no to product_discontinued table!");
		}
	}
	
	public function truncate_discontinued_table() {
		$this->result = $this->db->query("TRUNCATE TABLE product_discontinued");
		if ($this->result) {
			$this->output->info("Discontinued table truncated!");
		} else {
			$this->output->error("Failed to truncate product_discontinued table!");
		}
	}
	
	public function update_discontinued_table_by_file($filePath) {
		$this->result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE product_discontinued");
		if ($this->result) {
			$this->output->info("Discontinued table updated with $filePath!");
		} else {
			$this->output->error("Failed to update product_discontinued table with $filePath!");
		}
	}
	
	public function discontinued_table_record_count() {
		$this->result = $this->db->query("SELECT COUNT(*) FROM product_discontinued");
		$row = $this->result->fetch_row();
		$count = $row[0];
		return $count;
	}
}

?>

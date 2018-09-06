<?
/* Initialization */
require_once("init.php");
require_once("database.php");

class set_list {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $sku1, $sku2, $sku3, $sku4, $sku5, $sku6, $sku7, $sku8, $sku9, $sku10) {
		$result = $this->db->query("INSERT INTO set_list (id, code, item_no, sku1, sku2, sku3, sku4, sku5, sku6, sku7, sku8, sku9, sku10) VALUES ('', '$code', '$item_no', '$sku1', '$sku2', '$sku3', '$sku4', '$sku5', '$sku6', '$sku7', '$sku8', '$sku9', '$sku10')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code has been inserted successfully!");
		} else {
			$this->output->error("Failed to insert $item_no!");
		}
	}
	
	public function check($code, $item_no) {
		$result = $this->db->query("SELECT * FROM set_list WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code is a set!");
		} else {
			$this->output->info("Item: $item_no, Code: $code is not a set!");
		}
	}

	public function get_set($code, $item_no) {
		$result = $this->db->query("SELECT * FROM set_list WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			for ($i = 1; $i <= 10; $i++) {
				$count = "sku" . $i;
				$sku = $row[$count];
				if (!is_null($sku)) {
					$this->output->info("Item: $item_no, Code: $code has $sku!");
				}
			}
		} else {
			$this->output->info("Item: $item_no, Code: $code failed to get its set!");
		}
	}

	public function truncate_table() {
		$result = $this->db->query("TRUNCATE TABLE set_list");
		if ($result) {
			$this->output->info("Table truncated!");
		} else {
			$this->output->error("Failed to truncate!");
		}
	}
	
	public function update_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE set_list");
		if ($result) {
			$this->output->info("Table updated with $filePath!");
		} else {
			$this->output->error("Failed to update table with $filePath!");
		}
	}
	
	public function get_record_count() {
		$result = $this->db->query("SELECT * FROM set_list");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->info("There are $count records in table set_list!");
		} else {
			$this->output->error("Failed to get record count in table set_list!");
		}
	}
}

?>

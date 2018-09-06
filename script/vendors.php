<?
/* Initialization */
require_once("init.php");
require_once("database.php");

class vendors {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->output->debug_on();
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $name) {
		$result = $this->db->query("INSERT INTO vendors (code, name) VALUES ('$code', '$name')");
		if ($result) {
			$this->output->info("Vendor name: $name, Code: $code has been inserted successfully!");
		} else {
			$this->output->error("Failed to insert vendor!");
		}
	}
	
	public function check($code) {
		$result = $this->db->query("SELECT * FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			return true;
		} else {
			$this->output->error("Code: $code is not a valid vendor code!");
			return false;
		}
	}

	public function get_code($sku) {
		$pieces = explode("-", $sku);
		$code = $pieces[0];
		if ($this->check($code)) {
			return $code;
		} else {
			$this->output->error("Code: $code is not a valid vendor code!");
			return false;
		}
	}

	public function get_name($code) {
		$result = $this->db->query("SELECT name FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);	
			$name = $row['name'];
			$this->output->info("Name: $name for vendor code $code!");
			return $name;
		} else {
			$this->output->error("Code: $code is not a valid vendor code!");
			return false;
		}
	}

	public function truncate_table() {
		$result = $this->db->query("TRUNCATE TABLE vendors");
		if ($result) {
			$this->output->info("Table truncated!");
		} else {
			$this->output->error("Failed to truncate!");
		}
	}
	
	public function update_by_file($filePath) {
		$result = $this->db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE vendors");
		if ($result) {
			$this->output->info("Table updated with $filePath!");
		} else {
			$this->output->error("Failed to update table with $filePath!");
		}
	}
	
	public function get_record_count() {
		$result = $this->db->query("SELECT * FROM vendors");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->info("There are $count records in table vendors!");
		} else {
			$this->output->error("Failed to get record count in table vendors!");
		}
	}
}

?>

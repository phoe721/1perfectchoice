<?
/* Initialization */
require_once("database.php");

class vendors {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $name) {
		$result = $this->db->query("INSERT INTO vendors (code, name) VALUES ('$code', '$name')");
		if ($result) {
			$this->output->notice("Vendor name: $name, Code: $code has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert vendor!");
			return false;
		}
	}
	
	public function delete($code) {
		$result = $this->db->query("DELETE FROM vendors WHERE code = '$code'");
		if ($result) {
			$this->output->notice("Vendor Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Vendor Code: $code failed to delete!");
			return false;
		}
	}

	public function check($code) {
		$result = $this->db->query("SELECT * FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Code: $code is a valid vendor code!");
			return true;
		} else {
			$this->output->notice("Code: $code is not a valid vendor code!");
			return false;
		}
	}

	public function get_name($code) {
		$result = $this->db->query("SELECT name FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);	
			$name = $row['name'];
			$this->output->notice("Code: $code vendor name is $name!");
			return $name;
		} else {
			$this->output->notice("Code: $code is not a valid vendor code!");
			return false;
		}
	}

	public function get_record_count() {
		$result = $this->db->query("SELECT * FROM vendors");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table vendors!");
		} else {
			$this->output->error("Failed to get record count in table vendors!");
		}
	}
}

?>

<?
/* Initialization */
require_once("database.php");
require_once("init.php");

class vendors {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $name, $query_url, $per_box) {
		$result = $this->db->query("INSERT INTO vendors (code, name, query_url, per_box) VALUES ('$code', '$name', '$query_url', '$per_box')");
		if ($result) {
			$this->output->info("Code: $code, Name: $name, Per Box: $per_box - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Code: $code, Name: $name, Per Box: $per_box - Failed to insert!");
			return false;
		}
	}
	
	public function delete($code) {
		$result = $this->db->query("DELETE FROM vendors WHERE code = '$code'");
		if ($result) {
			$this->output->info("Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check($code) {
		$result = $this->db->query("SELECT * FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Code: $code - Valid vendor code!");
			return true;
		} else {
			$this->output->info("Code: $code - Not a valid vendor code!");
			return false;
		}
	}

	public function get_name($code) {
		$result = $this->db->query("SELECT name FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);	
			$name = $row['name'];
			$this->output->info("Code: $code - Vendor name is $name!");
			return $name;
		} else {
			$this->output->info("Code: $code - Vendor name not found!");
			return false;
		}
	}

	public function get_query_url($code) {
		$result = $this->db->query("SELECT query_url FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);	
			$query_url = $row['query_url'];
			$this->output->info("Code: $code - Query URL is $query_url!");
			return $query_url;
		} else {
			$this->output->info("Code: $code - Query URL not found!");
			return false;
		}
	}

	public function per_box($code) {
		$result = $this->db->query("SELECT per_box FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);	
			$per_box = $row['per_box'];
			$this->output->info("Code: $code - Per box is $per_box!");
			return $per_box;
		} else {
			$this->output->info("Code: $code - Per box not found!");
			return false;
		}
	}

	public function check_exist($code) {
		$result = $this->db->query("SELECT * FROM vendors WHERE code = '$code'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Code: $code - Not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM vendors");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table vendors!");
		} else {
			$this->output->error("Failed to get record count in table vendors!");
		}

		return $count;
	}
}
?>

<?
/* Initialization */
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

	public function insert($code, $item_no) {
		$result = $this->db->query("INSERT INTO discontinued (code, item_no) VALUES ('$code', '$item_no')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM discontinued WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function check($code, $item_no) {
		$result = $this->db->query("SELECT * FROM discontinued WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code is discontinued!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code is still active!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM discontinued");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table discontinued!");
		} else {
			$this->output->error("Failed to get record count in table discontinued!");
		}

		return $count;
	}
}

?>

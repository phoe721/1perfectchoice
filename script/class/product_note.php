<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("helper_functions.php");

class product_note {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $note) {
		$result = $this->db->query("INSERT INTO product_note (code, item_no, note) VALUES ('$code', '$item_no', '$note')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $note) {
		$result = $this->db->query("UPDATE product_note SET note = '$note' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM product_note WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_note($code, $item) {
		$note = '';
		$result = $this->db->query("SELECT note FROM product_note WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$note = $row['note'];
			$this->output->info("Item: $item, code: $code - Note: $note found!");
		} else {
			$this->output->info("Item: $item, code: $code - Note not found!");
		}

		return $note;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM product_note WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM product_note");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table product_note!");
		} else {
			$this->output->error("Failed to get record count in table product_note!");
		}

		return $count;
	}
}
?>

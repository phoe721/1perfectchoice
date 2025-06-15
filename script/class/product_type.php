<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("helper_functions.php");

class product_type {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $item_type) {
		$result = $this->db->query("INSERT INTO product_type (code, item_no, item_type) VALUES ('$code', '$item_no', '$item_type')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $item_type) {
		$result = $this->db->query("UPDATE product_type SET item_type = '$item_type' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM product_type WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_type($code, $item) {
		$item_type = '';
		$result = $this->db->query("SELECT item_type FROM product_type WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$item_type = $row['item_type'];
			$this->output->info("Item: $item, code: $code - Item Type: $item_type found!");
		} else {
			$this->output->info("Item: $item, code: $code - Item type not found!");
		}

		return $item_type;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM product_type WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM product_type");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table product_type!");
		} else {
			$this->output->error("Failed to get record count in table product_type!");
		}

		return $count;
	}
}
?>

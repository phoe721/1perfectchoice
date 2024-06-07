<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("helper_functions.php");

class inventory_ETA {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}
	
	public function insert($code, $item_no, $ETA) {
		$result = $this->db->query("INSERT INTO inventory_ETA (code, item_no, ETA) VALUES ('$code', '$item_no', '$ETA'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inventory ETA ($ETA) has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}

	public function update($code, $item_no, $ETA) {
		$result = $this->db->query("UPDATE inventory_ETA SET ETA = '$ETA' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inventory ETA has been updated to $ETA!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update inventory ETA!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM inventory_ETA WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$result = $this->db->query("SELECT * FROM inventory_ETA WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function check($code, $item_no) {
		list($code, $item_no) = replace_vendor($code, $item_no);
		$result = $this->db->query("SELECT ETA FROM inventory_ETA WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$ETA = $row['ETA'];
			$this->output->info("Item: $item_no, Code: $code - Inventory ETA is $ETA!");
		} else {
			$ETA = "";
			$this->output->info("Item: $item_no, Code: $code - Inventory ETA not found!");
		}

		return $ETA;
	}

	public function truncate() {
		$result = $this->db->query("TRUNCATE TABLE inventory_ETA");
		if ($result) {
			$this->output->info("Inventory ETA table has been truncated!");
			return true;
		} else {
			$this->output->info("Failed to truncate inventory ETA table!");
			return false;
		}
	}
}
?>

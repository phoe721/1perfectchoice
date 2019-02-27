<?
/* Initialization */
require_once("database.php");
require_once("set_list.php");

class dimensions {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
		$this->set_list = new set_list();
	}

	public function insert($code, $item_no, $length, $width, $height) {
		$result = $this->db->query("INSERT INTO dimensions (code, item_no, length, width, height) VALUES ('$code', '$item_no', '$length', '$width', '$height')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Dimensions $length x $width x $height has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $length, $width, $height) {
		$result = $this->db->query("UPDATE dimensions SET length = '$length', width = '$width', height = '$height' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Dimensions has been updated to $length x $width x $height!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to update dimensions!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_dimensions($code, $item_no) {
		$dimensions = array();
		$result = $this->db->query("SELECT length, width, height FROM dimensions WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$length = $row["length"];
			$width = $row["width"];
			$height = $row["height"];
			array_push($dimensions, $length, $width, $height);
			$this->output->notice("Item: $item_no, Code: $code - Dimensions $length x $width x $height found!");
		} else {
			array_push($dimensions, 0, 0, 0);
			$this->output->notice("Item: $item_no, Code: $code - Dimensions not found!");
		}

		return $dimensions;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM dimensions");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table dimensions!");
		} else {
			$this->output->error("Failed to get record count in table dimensions!");
		}

		return $count;
	}
}

?>

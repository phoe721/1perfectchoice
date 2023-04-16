<?
/* Initialization */
require_once("database.php");
require_once("init.php");
require_once("helper_functions.php");

class seat_attribute {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $seat_width, $seat_depth, $seat_height, $back_height, $seat_back_width, $seat_back_height, $seat_bottom_depth, $seat_bottom_width, $seat_bottom_thickness, $maximum_seat_height, $minimum_seat_height) {
		$result = $this->db->query("INSERT INTO seat_attribute (code, item_no, seat_width, seat_depth, seat_height, back_height, seat_back_width, seat_back_height, seat_bottom_depth, seat_bottom_width, seat_bottom_thickness, maximum_seat_height, minimum_seat_height) VALUES ('$code', '$item_no', '$seat_width', '$seat_depth', '$seat_height', '$back_height', '$seat_back_width', '$seat_back_height', '$seat_bottom_depth', '$seat_bottom_width', '$seat_bottom_thickness', '$maximum_seat_height', '$minimum_seat_height')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $seat_width, $seat_depth, $seat_height, $back_height, $seat_back_width, $seat_back_height, $seat_bottom_depth, $seat_bottom_width, $seat_bottom_thickness, $maximum_seat_height, $minimum_seat_height) {
		$result = $this->db->query("UPDATE seat_attribute SET seat_width = '$seat_width', seat_depth = '$seat_depth', seat_height = '$seat_height', back_height = '$back_height', seat_back_width = '$seat_back_width', seat_back_height = '$seat_back_height', seat_bottom_depth = '$seat_bottom_depth', seat_bottom_width = '$seat_bottom_width', seat_bottom_thickness = '$seat_bottom_thickness', maximum_seat_height = '$maximum_seat_height', minimum_seat_height = '$minimum_seat_height' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM seat_attribute WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_seat_width($code, $item) {
		$seat_width = '';
		$result = $this->db->query("SELECT seat_width FROM seat_attribute WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$seat_width = $row['seat_width'];
			$this->output->info("Item: $item, code: $code - Seat width: $seat_width found!");
		} else {
			$this->output->info("Item: $item, code: $code - Seat width not found!");
		}

		return $seat_width;
	}

	public function get_seat_depth($code, $item) {
		$seat_depth = '';
		$result = $this->db->query("SELECT seat_depth FROM seat_attribute WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$seat_depth = $row['seat_depth'];
			$this->output->info("Item: $item, code: $code - Seat depth: $seat_depth found!");
		} else {
			$this->output->info("Item: $item, code: $code - Seat depth not found!");
		}

		return $seat_depth;
	}

	public function get_seat_height($code, $item) {
		$seat_height = '';
		$result = $this->db->query("SELECT seat_height FROM seat_attribute WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$seat_height = $row['seat_height'];
			$this->output->info("Item: $item, code: $code - Seat height: $seat_height found!");
		} else {
			$this->output->info("Item: $item, code: $code - Seat height not found!");
		}

		return $seat_height;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM seat_attribute WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM seat_attribute");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table seat_attribute!");
		} else {
			$this->output->error("Failed to get record count in table seat_attribute!");
		}

		return $count;
	}
}
?>

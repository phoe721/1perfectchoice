<?
/* Initialization */
require_once("database.php");

class product {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = new database;
		$this->db->connect(DB_SERVER, DB_USER, DB_PASS, DATABASE);
		mysqli_set_charset($this->db->getConnection(), "utf8");
	}

	public function insert($code, $item_no, $item_type, $upc, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $color, $material) {
		$result = $this->db->query("INSERT INTO product (code, item_no, item_type, upc, title, description, feature1, feature2, feature3, feature4, feature5, color, material) VALUES ('$code', '$item_no', '$item_type', '$upc', '$title', '$description', '$feature1', '$feature2', '$feature3', '$feature4', '$feature5', '$color', '$material')");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $item_type, $upc, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $color, $material) {
		$result = $this->db->query("UPDATE product SET item_type = '$item_type', upc = '$upc', title = '$title', description = '$description', feature1 = '$feature1', feature2 = '$feature2', feature3 = '$feature3', feature4 = '$feature4', feature5 = '$feature5', color = '$color', material = '$material' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM product WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code - Deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Failed to delete!");
			return false;
		}
	}

	public function get_title($code, $item) {
		$title = '';
		$result = $this->db->query("SELECT title FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$title = $row['title'];
			$this->output->notice("Item: $item, code: $code - Title: $title found!");
		} else {
			$this->output->notice("Item: $item, code: $code - Title not found!");
		}

		return $title;
	}

	public function get_description($code, $item) {
		$description = '';
		$result = $this->db->query("SELECT description FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$description = $row['description'];
			$this->output->notice("Item: $item, Code: $code - Description: $description found!");
		} else {
			$this->output->notice("Item: $item, Code: $code - Description not found!");
		}

		return $description;
	}

	public function get_color($code, $item) {
		$color = '';
		$result = $this->db->query("SELECT color FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$color = $row['color'];
			$this->output->notice("Item: $item, Code: $code - Color: $color found!");
		} else {
			$this->output->notice("Item: $item, Code: $code - Color not found!");
		}

		return $color;
	}

	public function get_material($code, $item) {
		$material = '';
		$result = $this->db->query("SELECT material FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$material = $row['material'];
			$this->output->notice("Item: $item, Code: $code product - Material: $material found!");
		} else {
			$this->output->notice("Item: $item, Code: $code product - Material not found!");
		}

		return $material;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM product WHERE code = '$code' AND item_no = '$item_no'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->notice("Item: $item_no, Code: $code - Exists!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code - Not exist!");
			return false;
		}
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT COUNT(*) AS total FROM product");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->notice("There are $count records in table product!");
		} else {
			$this->output->error("Failed to get record count in table product!");
		}

		return $count;
	}
}
?>

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
			$this->output->notice("Item: $item_no, Code: $code has been inserted successfully!");
			return true;
		} else {
			$this->output->notice("Failed to insert $item_no!");
			return false;
		}
	}
	
	public function update($code, $item_no, $item_type, $upc, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $color, $material) {
		$result = $this->db->query("UPDATE product SET item_type = '$item_type', upc = '$upc', title = '$title', description = '$description', feature1 = '$feature1', feature2 = '$feature2', feature3 = '$feature3', feature4 = '$feature4', feature5 = '$feature5', color = '$color', material = '$material' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been updated!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to update product!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM product WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->notice("Item: $item_no, Code: $code has been deleted!");
			return true;
		} else {
			$this->output->notice("Item: $item_no, Code: $code failed to delete!");
			return false;
		}
	}

	public function get_upc($code, $item) {
		$upc = '';
		$result = $this->db->query("SELECT upc FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$upc = $row['upc'];
			$this->output->notice("Item: $item, code: $code product $upc!");
		} else {
			$this->output->notice("Item: $item, code: $code product not found!");
		}

		return $upc;
	}

	public function get_record_count() {
		$count = -1;
		$result = $this->db->query("SELECT * FROM product");
		if ($result) {
			$count = mysqli_num_rows($result);
			$this->output->notice("There are $count records in table product!");
		} else {
			$this->output->error("Failed to get record count in table product!");
		}

		return $count;
	}
}

?>

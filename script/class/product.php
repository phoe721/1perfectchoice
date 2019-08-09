<?
/* Initialization */
require_once("database.php");

class product {
	private $db;
	private $output;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}

	public function insert($code, $item_no, $item_type, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $feature6, $feature7, $feature8, $feature9, $feature10, $color, $material) {
		$result = $this->db->query("INSERT INTO product (code, item_no, item_type, title, description, feature1, feature2, feature3, feature4, feature5, feature6, feature7, feature8, feature9, feature10, color, material) VALUES ('$code', '$item_no', '$item_type', '$title', '$description', '$feature1', '$feature2', '$feature3', '$feature4', '$feature5', '$feature6', '$feature7', '$feature8', '$feature9', '$feature10', '$color', '$material')");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Inserted successfully!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to insert!");
			return false;
		}
	}
	
	public function update($code, $item_no, $item_type, $title, $description, $feature1, $feature2, $feature3, $feature4, $feature5, $feature6, $feature7, $feature8, $feature9, $feature10, $color, $material) {
		$result = $this->db->query("UPDATE product SET item_type = '$item_type', title = '$title', description = '$description', feature1 = '$feature1', feature2 = '$feature2', feature3 = '$feature3', feature4 = '$feature4', feature5 = '$feature5', feature6 = '$feature6', feature7 = '$feature7', feature8 = '$feature8', feature9 = '$feature9', feature10 = '$feature10', color = '$color', material = '$material' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update!");
			return false;
		}
	}

	public function update_title($code, $item_no, $title) {
		$result = $this->db->query("UPDATE product SET title = '$title' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Title updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update title!");
			return false;
		}
	}

	public function update_description($code, $item_no, $description) {
		$result = $this->db->query("UPDATE product SET description = '$description' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Description updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update description!");
			return false;
		}
	}

	public function update_features($code, $item_no, $features) {
		list($feature1, $feature2, $feature3, $feature4, $feature5, $feature6, $feature7, $feature8, $feature9, $feature10) = explode(",", $features);
		$result = $this->db->query("UPDATE product SET feature1 = '$feature1', feature2 = '$feature2', feature3 = '$feature3', feature4 = '$feature4', feature5 = '$feature5', feature6 = '$feature6', feature7 = '$feature7', feature8 = '$feature8', feature9 = '$feature9', feature10 = '$feature10' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Features updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update features!");
			return false;
		}
	}

	public function update_color($code, $item_no, $color) {
		$result = $this->db->query("UPDATE product SET color = '$color' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Color updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update color!");
			return false;
		}
	}

	public function update_material($code, $item_no, $material) {
		$result = $this->db->query("UPDATE product SET material = '$material' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Material updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update material!");
			return false;
		}
	}

	public function update_item_type($code, $item_no, $item_type) {
		$result = $this->db->query("UPDATE product SET item_type = '$item_type' WHERE code = '$code' AND item_no = '$item_no'");
		if ($result) {
			$this->output->info("Item: $item_no, Code: $code - Item type updated!");
			return true;
		} else {
			$this->output->info("Item: $item_no, Code: $code - Failed to update item type!");
			return false;
		}
	}

	public function delete($code, $item_no) {
		$result = $this->db->query("DELETE FROM product WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT item_type FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$item_type = $row['item_type'];
			$this->output->info("Item: $item, code: $code - Item Type: $item_type found!");
		} else {
			$this->output->info("Item: $item, code: $code - Item type not found!");
		}

		return $item_type;
	}

	public function get_title($code, $item) {
		$title = '';
		$result = $this->db->query("SELECT title FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$title = $row['title'];
			$this->output->info("Item: $item, code: $code - Title: $title found!");
		} else {
			$this->output->info("Item: $item, code: $code - Title not found!");
		}

		return $title;
	}

	public function get_description($code, $item) {
		$description = '';
		$result = $this->db->query("SELECT description FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$description = $row['description'];
			$this->output->info("Item: $item, Code: $code - Description: $description found!");
		} else {
			$this->output->info("Item: $item, Code: $code - Description not found!");
		}

		return $description;
	}

	public function get_features($code, $item) {
		$features = array();
		$result = $this->db->query("SELECT feature1, feature2, feature3, feature4, feature5, feature6, feature7, feature8, feature9, feature10 FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			for ($i = 1; $i <= 10; $i++) {
				$feature = $row["feature" . $i];
				if (!empty($feature)) {
					array_push($features, $feature);
					$this->output->info("Item: $item, Code: $code - Feature $i: $feature found!");
				}
			}
		} else {
			$this->output->info("Item: $item, Code: $code - Features not found!");
		}

		return $features;
	}

	public function get_color($code, $item) {
		$color = '';
		$result = $this->db->query("SELECT color FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$color = $row['color'];
			$this->output->info("Item: $item, Code: $code - Color: $color found!");
		} else {
			$this->output->info("Item: $item, Code: $code - Color not found!");
		}

		return $color;
	}

	public function get_material($code, $item) {
		$material = '';
		$result = $this->db->query("SELECT material FROM product WHERE code = '$code' AND item_no = '$item'");
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_array($result);
			$material = $row['material'];
			$this->output->info("Item: $item, Code: $code - Material: $material found!");
		} else {
			$this->output->info("Item: $item, Code: $code - Material not found!");
		}

		return $material;
	}

	public function check_exist($code, $item_no) {
		$result = $this->db->query("SELECT * FROM product WHERE code = '$code' AND item_no = '$item_no'");
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
		$result = $this->db->query("SELECT COUNT(*) AS total FROM product");
		if ($result) {
			$row = mysqli_fetch_array($result);
			$count = $row['total'];
			$this->output->info("There are $count records in table product!");
		} else {
			$this->output->error("Failed to get record count in table product!");
		}

		return $count;
	}
}
?>

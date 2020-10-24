<?
/* Initialization */
require_once("database.php");

class orders {
	private $db;
	private $output;
	private $set_list;

	public function __construct() {
		$this->output = new debugger;
		$this->db = database::getInstance();
	}
	
	public function insert($id, $code, $item_no, $city, $state, $country, $price, $qty, $date, $platform) {
		$result = $this->db->query("INSERT INTO orders (id, code, item_no, city, state, country, price, qty, date, platform) VALUES ('$id', '$code', '$item_no', '$city', '$state', '$country', '$price', '$qty', '$date', '$platform')");
		if ($result) {
			$this->output->info("Order ID: $id - Order has been inserted successfully!");
			return true;
		} else {
			$this->output->info("Order ID: $id - Failed to insert!");
			return false;
		}
	}

	public function update($id, $code, $item_no, $city, $state, $country, $price, $qty, $date, $platform) {
		$result = $this->db->query("UPDATE orders SET code = '$code', item_no = '$item_no', city = '$city', state = '$state', country = '$country', price = '$price', qty = '$qty', date = '$date', platform = '$platform' WHERE id = '$id'");
		if ($result) {
			$this->output->info("Order ID: $id - Order has been updated successfully!");
			return true;
		} else {
			$this->output->info("Order ID: $id - Failed to update!");
			return false;
		}
	}

	public function delete($id) {
		$result = $this->db->query("DELETE FROM orders WHERE id = '$id'");
		if ($result) {
			$this->output->info("Order ID: $id - Order deleted!");
			return true;
		} else {
			$this->output->info("Order ID: $id - Failed to delete!");
			return false;
		}
	}

	public function check_exist($id) {
		$result = $this->db->query("SELECT * FROM orders WHERE id = '$id'");
		if (mysqli_num_rows($result) > 0) {
			$this->output->info("Order ID: $id - Order exists!");
			return true;
		} else {
			$this->output->info("Order ID: $id - Not exist!");
			return false;
		}
	}
}
?>

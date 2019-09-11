<?
require_once("class/UPC.php");
require_once("class/discontinued.php");
require_once("class/costs.php");
require_once("class/inventory.php");
require_once("class/dimensions.php");
require_once("class/weights.php");
require_once("class/packages.php");
require_once("class/product.php");
require_once("class/set_list.php");
$UPC = new UPC();
$discontinued = new discontinued();
$costs = new costs();
$dimensions = new dimensions();
$weights = new weights();
$inventory = new inventory();
$packages = new packages();
$product = new product();
$set_list = new set_list();

$result = "";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"]) && isset($_POST["field"]) && isset($_POST["value"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$field = $_POST["field"];
	$value = $_POST["value"];

	if ($field == "upc") {
		if ($UPC->check_exist($code, $item_no)) {
			if (empty($value)) {
				$result = $UPC->delete($code, $item_no);
			} else {
				$result = $UPC->update($code, $item_no, $value);
			}
		} else {
			$result = $UPC->insert($code, $item_no, $value);
		}
	} else if ($field == "cost") {
		if ($costs->check_exist($code, $item_no)) {
			if (empty($value)) {
				$result = $costs->delete($code, $item_no);
			} else {
				$result = $costs->update_cost($code, $item_no, $value);
			}
		} else {
			$result = $costs->insert($code, $item_no, $value, 1); // For now, fix later
		}
	} else if ($field == "unit") {
		if ($costs->check_exist($code, $item_no)) {
			$result = $costs->update_unit($code, $item_no, $value);
		}
	} else if ($field == "discontinued") {
		if ($value == "Active") {
			$result = $discontinued->delete($code, $item_no);
		} else if ($value == "Discontinued") {
			$result = $discontinued->insert($code, $item_no);
		}
	} else if ($field == "item_type") {
		$result = $product->update_item_type($code, $item_no, $value);
	} else if ($field == "title") {
		$result = $product->update_title($code, $item_no, $value);
	} else if ($field == "color") {
		$result = $product->update_color($code, $item_no, $value);
	} else if ($field == "material") {
		$result = $product->update_material($code, $item_no, $value);
	} else if ($field == "features") {
		$result = $product->update_features($code, $item_no, $value);
	} else if ($field == "description") {
		$result = $product->update_description($code, $item_no, $value);
	} else if ($field == "set_list") {
		if ($set_list->check($code, $item_no)) {
			if (!empty($value)) {
				$item = explode(",", $value);
				for ($i = 0; $i < 10; $i++) if (!isset($item[$i])) $item[$i] = NULL;
				$result = $set_list->update($code, $item_no, $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], $item[8], $item[9]); 
			} else {
				$result = $set_list->delete($code, $item_no);
			}
		} else {
			if (!empty($value)) {
				$item = explode(",", $value);
				for ($i = 0; $i < 10; $i++) if (!isset($item[$i])) $item[$i] = NULL;
				$result = $set_list->insert($code, $item_no, $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], $item[8], $item[9]); 
			}
		}
	} else if ($field == "qty") {
		if ($inventory->check_exist($code, $item_no)) {
			$result = $inventory->update($code, $item_no, $value);
		} else {
			$result = $inventory->insert($code, $item_no, $value);
		}
	} else if ($field == "dimensions") {
		list($length, $width, $height) = explode(",", $value);
		if ($dimensions->check_exist($code, $item_no)) {
			$result = $dimensions->update($code, $item_no, $length, $width, $height);
		} else {
			$result = $dimensions->insert($code, $item_no, $length, $width, $height);
		}
	} else if ($field == "weight") {
		if ($weights->check_exist($code, $item_no)) {
			$result = $weights->update($code, $item_no, $value);
		} else {
			$result = $weights->insert($code, $item_no, $value);
		}
	} else if ($field == "pg_dimension") {
		$dimension = explode(",", $value);
		for ($i = 0; $i < 15; $i++) if (!isset($dimension[$i])) $dimension[$i] = NULL;
		if ($packages->check_exist($code, $item_no)) {
			$result = $packages->update_dimensions($code, $item_no, $dimension[0], $dimension[1], $dimension[2], $dimension[3], $dimension[4], $dimension[5], $dimension[6], $dimension[7], $dimension[8], $dimension[9], $dimension[10], $dimension[11], $dimension[12], $dimension[13], $dimension[14]); 
		} else {
			$result = $packages->insert_dimensions($code, $item_no, $dimension[0], $dimension[1], $dimension[2], $dimension[3], $dimension[4], $dimension[5], $dimension[6], $dimension[7], $dimension[8], $dimension[9], $dimension[10], $dimension[11], $dimension[12], $dimension[13], $dimension[14]); 
		}
	} else if ($field == "pg_weight") {
		$weight = explode(",", $value);
		for ($i = 0; $i < 5; $i++) if (!isset($weight[$i])) $weight[$i] = NULL;
		if ($packages->check_exist($code, $item_no)) {
			$result = $packages->update_weights($code, $item_no, $weight[0], $weight[1], $weight[2], $weight[3], $weight[4]); 
		} else {
			$result = $packages->insert_weights($code, $item_no, $weight[0], $weight[1], $weight[2], $weight[3], $weight[4]); 
		}
	} 

	echo json_encode($result);
}
?>

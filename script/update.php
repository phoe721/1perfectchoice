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
	$field = $_POST["field"];
	$value = $_POST["value"];
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);

	if ($field == "upc") {
		$result = $UPC->update($code, $item_no, $value);
	} 

	if ($field == "discontinued") {
		if ($value == "Active") {
			$result = $discontinued->delete($code, $item_no);
		} else if ($value == "Discontinued") {
			$result = $discontinued->insert($code, $item_no);
		}
	} 

	if ($field == "item_type") {
		$result = $product->update_item_type($code, $item_no, $value);
	} 

	if ($field == "title") {
		$result = $product->update_title($code, $item_no, $value);
	} 

	if ($field == "color") {
		$result = $product->update_color($code, $item_no, $value);
	} 

	if ($field == "material") {
		$result = $product->update_material($code, $item_no, $value);
	} 

	if ($field == "features") {
		$result = $product->update_features($code, $item_no, $value);
	} 

	if ($field == "description") {
		$result = $product->update_description($code, $item_no, $value);
	} 

	if ($field == "set_list") {
		$result = $set_list->update($code, $item_no, $value);
	} 

	if ($field == "qty") {
		$result = $inventory->update($code, $item_no, $value);
	} 

	if ($field == "dimensions") {
		$result = $dimensions->update($code, $item_no, $value);
	} 

	if ($field == "weights") {
		$result = $weights->update($code, $item_no, $value);
	} 

	echo json_encode($result);
}
?>

<?
require_once("class/ASIN.php");
require_once("class/UPC.php");
require_once("class/costs.php");
require_once("class/inventory.php");
require_once("class/discontinued.php");
require_once("class/dimensions.php");
require_once("class/weights.php");
require_once("class/packages.php");
require_once("class/product.php");
require_once("class/set_list.php");
require_once("class/shipping.php");
require_once("class/vendors.php");
require_once("class/validator.php");
$ASIN = new ASIN();
$UPC = new UPC();
$costs = new costs();
$discontinued = new discontinued();
$dimensions = new dimensions();
$weights = new weights();
$inventory = new inventory();
$packages = new packages();
$product = new product();
$set_list = new set_list();
$shipping = new shipping();
$vendors = new vendors();
$validator = new validator();
$data = array();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$vendor = $vendors->get_name($code);
	$query_url = $vendors->get_query_url($code) . $item_no;
	$asin = $ASIN->get_asin($code, $item_no);
	$title = $product->get_title($code, $item_no);
	$description = $product->get_description($code, $item_no);
	$type = $product->get_type($code, $item_no);
	$features = $product->get_features($code, $item_no);
	$features_str = "";
	for ($i = 0; $i < count($features); $i++) {
		$count = $i + 1;
		$features_str .= "Feature $count: " . $features[$i] . "<br>";
	}
	$color = $product->get_color($code, $item_no);
	$material = $product->get_material($code, $item_no);
	$upc = $UPC->get_upc($code, $item_no);
	$status = $discontinued->check($code, $item_no) ? "Discontinued" : "Active";
	$cost = $costs->get_cost($code, $item_no);
	$unit = $costs->get_unit($code, $item_no);
	$updated_time = $costs->get_updated_time($code, $item_no);
	$img_url = IMAGE_SERVER . "$code/$item_no.jpg";
	$qty = $inventory->get($code, $item_no);
	$set = $set_list->get_set($code, $item_no);
	$set_str = $set ? implode(", ", $set) : "No";
	$weight = array_sum($weights->get_weight($code, $item_no));
	$dimension = $dimensions->get_dimensions($code, $item_no);
	$dimension_str = $dimension ? implode(" x ", $dimension) : "";
	$box_count = $packages->get_box_count($code, $item_no); 
	$package_weight = $packages->get_weight($code, $item_no);
	$total_package_weight = array_sum($package_weight);
	$package_weight_str = "";
	for ($i = 0; $i < $box_count; $i++) {
		$count = $i + 1;
		$package_weight_str .= "Box $count Weight: " . $package_weight[$i] . "<br>";
	}
	$package_dimension = $packages->get_dimensions($code, $item_no);
	$package_dimension_str = "";
	for ($i = 0; $i < $box_count; $i++) {
		$count = $i + 1;
		$package_dimension_str .= "Box $count Dimension: ";
		$package_dimension_str .= $package_dimension[$i] . " x " . $package_dimension[$i+1] . " x " . $package_dimension[$i+2];
		$package_dimension_str .= "<br>";
	}

	$data['img_url'] = $img_url;
	$data['vendor'] = $vendor;
	$data['query_url'] = $query_url;
	$data['sku'] = $sku;
	$data['upc'] = $upc;
	$data['asin'] = $asin;
	$data['status']= $status;
	$data['set_list'] = $set_str;
	$data['cost'] = $cost;
	$data['updated_time'] = $updated_time;
	$data['unit'] = $unit;
	$data['quantity'] = $qty;
	$data['title'] = $title;
	$data['color'] = $color;
	$data['material'] = $material;
	$data['features'] = $features_str;
	$data['description'] = $description;
	$data['weight'] = $weight;
	$data['dimension'] = $dimension_str;
	$data['packageWeight'] = $package_weight_str;
	$data['totalPackageWeight'] = $total_package_weight;
	$data['packageDimension'] = $package_dimension_str;
	$data['boxCount'] = $box_count;

	echo json_encode($data);
}
?>

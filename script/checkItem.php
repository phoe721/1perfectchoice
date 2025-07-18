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
require_once("class/product_type.php");
require_once("class/product_note.php");
require_once("class/set_list.php");
require_once("class/shipping.php");
require_once("class/vendors.php");
require_once("class/manufacturing_country.php");
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
$product_type = new product_type();
$product_note = new product_note();
$set_list = new set_list();
$shipping = new shipping();
$vendors = new vendors();
$manufacturing_country = new manufacturing_country();
$validator = new validator();
$data = $features = array();
$item_type = $title = $description = $color = $material = $note = $img_dim = $img_wb_dim = $origin = $error = $warning = "";
if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["input"])) || ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["input"]))) { 
	$input = empty($_POST["input"]) ? $_GET["input"] : $_POST["input"];
	if ($validator->check_asin($input)) {
		$sku = $ASIN->get_sku($input);
	} else if ($validator->check_upc($input)) {
		$sku = $UPC->get_sku($input);
	} else if ($validator->check_sku($input)) {
		$sku = $input;
	}

	if (!empty($sku) && strpos($sku, "-")) {
		list($code, $item_no) = explode("-", $sku, 2);
		$vendor = $vendors->get_name($code);
		$query_url = $vendors->get_query_url($code) . $item_no;
		$asin = $ASIN->get_asin($code, $item_no);
		$asin_url = "https://www.amazon.com/dp/" . $asin;
		if (!$product->check_exist($code, $item_no)) { 
			$warning .= "Proudct information not found! ";
		} else {
			$title = $product->get_title($code, $item_no);
			$description = $product->get_description($code, $item_no);
			$item_type = $product_type->get_type($code, $item_no);
			$features = $product->get_features($code, $item_no);
			$color = $product->get_color($code, $item_no);
			$material = $product->get_material($code, $item_no);
		}
		$note = $product_note->get_note($code, $item_no);
		$upc = $UPC->get_upc($code, $item_no);
		$status = $discontinued->check($code, $item_no) ? "Discontinued" : "Active";
		$cost = $costs->get_cost($code, $item_no);
		$unit = $costs->get_unit($code, $item_no);
		$cost_updated_time = $costs->get_updated_time($code, $item_no);
		$img_url = IMAGE_SERVER . "$code/$item_no.jpg";
		//$img_wb_url = IMAGE_SERVER . "$code" . "_WB/$item_no.jpg";
		$qty = $inventory->get($code, $item_no);
		$inventory_updated_time = $inventory->get_updated_time($code, $item_no);
		$set = $set_list->get_set($code, $item_no);
		$weight = array_sum($weights->get_weight($code, $item_no));
		$dimension = $dimensions->get_dimensions($code, $item_no);
		$box_count = $packages->get_box_count($code, $item_no); 
		$package_weight = $packages->get_weight($code, $item_no);
		$total_package_weight = round(array_sum($package_weight), 2);
		$package_dimension = $packages->get_dimensions($code, $item_no);
		$origin = $manufacturing_country->get_origin($code, $item_no);

		$data['error'] = $error;	
		$data['warning'] = $warning;	
		$data['img_url'] = $img_url;
		//$data['img_wb_url'] = $img_wb_url;
		$data['vendor'] = $vendor;
		$data['query_url'] = $query_url;
		$data['sku'] = $sku;
		$data['upc'] = $upc;
		$data['asin'] = $asin;
		$data['asin_url'] = $asin_url;
		$data['item_type'] = $item_type;
		$data['status']= $status;
		$data['set_list'] = $set;
		$data['cost'] = $cost;
		$data['cost_updated_time'] = $cost_updated_time;
		$data['unit'] = $unit;
		$data['quantity'] = $qty;
		$data['inventory_updated_time'] = $inventory_updated_time;
		$data['title'] = $title;
		$data['color'] = $color;
		$data['material'] = $material;
		$data['features'] = $features;
		$data['description'] = $description;
		$data['note'] = $note;
		$data['weight'] = $weight;
		$data['dimension'] = $dimension;
		$data['packageWeight'] = $package_weight;
		$data['totalPackageWeight'] = $total_package_weight;
		$data['packageDimension'] = $package_dimension;
		$data['boxCount'] = $box_count;
		$data['manufacturing_country'] = $origin;
	} else {
		$data['error'] = "SKU not found!";
	}

	echo json_encode($data);
}
?>

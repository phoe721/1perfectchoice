<?
require_once("class/ASIN.php");
require_once("class/costs.php");
require_once("class/inventory.php");
require_once("class/discontinued.php");
require_once("class/dimensions.php");
require_once("class/packages.php");
require_once("class/product.php");
require_once("class/set_list.php");
require_once("class/shipping.php");
require_once("class/status.php");
require_once("class/vendors.php");
require_once("class/validator.php");
$a = new ASIN();
$c = new costs();
$dis = new discontinued();
$dim = new dimensions();
$inv = new inventory();
$pg = new packages();
$p = new product();
$sl = new set_list();
$sh = new shipping();
$v = new vendors();
$status = new status();
$validator = new validator();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$vendor = $v->check_exist($code) ? $v->get_name($code) : "Not Found";
	$asin = $a->check_exist($code, $item_no) ? $a->get_asin($code, $item_no) : "Not Found";
	$title = $p->check_exist($code, $item_no) ? $p->get_title($code, $item_no) : "Not Found";
	$description = $p->check_exist($code, $item_no) ? $p->get_description($code, $item_no) : "Not Found";
	$color = $p->check_exist($code, $item_no) ? $p->get_color($code, $item_no) : "Not Found";
	$material = $p->check_exist($code, $item_no) ? $p->get_material($code, $item_no) : "Not Found";
	$upc = $p->check_exist($code, $item_no) ? $p->get_upc($code, $item_no) : "Not Found";
	$discontinued = $dis->check($code, $item_no) ? "Discontinued" : "Active";
	$cost = $c->check_exist($code, $item_no) ? $c->get_cost($code, $item_no) : "Not Found";
	$unit = $c->check_exist($code, $item_no) ? $c->get_unit($code, $item_no) : "Not Found";
	$url = IMAGE_SERVER . "$code/$item_no.jpg";
	$img = ($validator->check_url($url)) ? "<img src='$url' width='300px' alt='$sku'>" : "<img src='' alt='Not Found'>";
	$qty = $inv->check_exist($code, $item_no) ? $inv->get($code, $item_no) : "Not Found";
	$set = $sl->get_set($code, $item_no);
	$set_str = $set ? implode(", ", $set) : "No";

	$result = "<div style='float: left;margin: 5px'>" . $img . "</div>";
	$result .= "<div style='float: left;margin: 5px;'>";
	$result .= "SKU: $sku<br>";
	$result .= "Vendor: $vendor<br>";
	$result .= "ASIN: $asin<br>";
	$result .= "UPC: $upc<br>";
	$result .= "Status: $discontinued<br>";
	$result .= "Cost: $cost<br>";
	$result .= "Unit: $unit<br>";
	$result .= "Quantity: $qty<br>";
	$result .= "Color: $color<br>";
	$result .= "Material: $material<br>";
	$result .= "Set List: $set_str<br>";

	if ($dim->check_exist($code, $item_no)) {
		$weight = $dim->get_weight($code, $item_no);
		$dimensions = $dim->get_dimensions($code, $item_no);

		if ($set) {
			for ($i = 0; $i < count($set); $i++) {
				$item = $set[$i];
				$result .= "Item $item Weight: " . $weight[$i] . " lbs<br>";
				$result .= "Item $item Dimensions: " . $dimensions[$i*3] . " x " . $dimensions[$i*3+1] . " x " . $dimensions[$i*3+2] . "<br>";
			}
		} else {
			$result .= "Weight: $weight[0]<br>";
			$result .= "Dimensions: " . implode(" x ", $dimensions) . "<br>";
		}
	} else {
		$result .= "Weight: Not Found<br>";
		$result .= "Dimensions: Not Found<br>";
	}

	if ($pg->check_exist($code, $item_no)) {
		$box_count = $pg->get_box_count($code, $item_no); 
		$pg_weights = $pg->get_weight($code, $item_no);
		$pg_dimensions = $pg->get_dimensions($code, $item_no);

		$result .= "Box Count: $box_count<br>";
		for ($i = 0; $i < $box_count; $i++) {
			$count = $i + 1;
			$result .= "Box $count Weight: " . $pg_weights[$i] . " lbs<br>";
			$result .= "Box $count Dimensions: " . $pg_dimensions[$i*3] . " x " . $pg_dimensions[$i*3+1] . " x " . $pg_dimensions[$i*3+2] . "<br>";
		}
	} else {
		$result .= "Box Count: Not Found<br>";
		$result .= "Box Weight: Not Found<br>";
		$result .= "Box Dimensions: Not Found<br>";
	}

	$result .= "</div>";

	echo json_encode($result);
}
?>

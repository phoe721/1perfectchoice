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
require_once("class/status.php");
require_once("class/vendors.php");
require_once("class/validator.php");
$a = new ASIN();
$u = new UPC();
$c = new costs();
$dis = new discontinued();
$dim = new dimensions();
$w = new weights();
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
	$vendor = $v->get_name($code);
	$asin = $a->get_asin($code, $item_no);
	$title = $p->get_title($code, $item_no);
	$description = $p->get_description($code, $item_no);
	$color = $p->get_color($code, $item_no);
	$material = $p->get_material($code, $item_no);
	$upc = $u->get_upc($code, $item_no);
	$discontinued = $dis->check($code, $item_no) ? "Discontinued" : "Active";
	$cost = $c->get_cost($code, $item_no);
	$unit = $c->get_unit($code, $item_no);
	$url = IMAGE_SERVER . "$code/$item_no.jpg";
	$img = ($validator->check_url($url)) ? "<img src='$url' width='300px' alt='$sku'>" : "<img src='' alt='Not Found'>";
	$qty = $inv->get($code, $item_no);
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

	$weight = $w->get_weight($code, $item_no);
	$dimensions = $dim->get_dimensions($code, $item_no);

	if ($set) {
		for ($i = 0; $i < count($set); $i++) {
			$item = $set[$i];
			$result .= "Item $item Weight: " . $weight[$i] . " lbs<br>";
			$result .= "Item $item Dimensions: " . $dimensions[$i*3] . " x " . $dimensions[$i*3+1] . " x " . $dimensions[$i*3+2] . "<br>";
		}
	} else {
		$result .= "Weight: $weight<br>";
		$result .= "Dimensions: " . implode(" x ", $dimensions) . "<br>";
	}

	$box_count = $pg->get_box_count($code, $item_no); 
	$pg_weights = $pg->get_weight($code, $item_no);
	$pg_dimensions = $pg->get_dimensions($code, $item_no);
	$total_weight = 0;

	$result .= "Box Count: $box_count<br>";
	for ($i = 0; $i < $box_count; $i++) {
		$count = $i + 1;
		$total_weight += $pg_weights[$i];
		$result .= "Box $count Weight: " . $pg_weights[$i] . " lbs<br>";
		$result .= "Box $count Dimensions: " . $pg_dimensions[$i*3] . " x " . $pg_dimensions[$i*3+1] . " x " . $pg_dimensions[$i*3+2] . "<br>";
	}
	$result .= "Total Weight: $total_weight lbs<br>";

	$result .= "</div>";

	echo json_encode($result);
}
?>

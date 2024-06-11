<?
require_once("class/validator.php");
$validator = new validator();
$data = array();
$error = "";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["input"])) { 
	$input = empty($_POST["input"]) ? $_GET["input"] : $_POST["input"];
	$sku = $input;
	$img_url = empty($_POST["product_img"]) ? $_GET["product_img"] : $_POST["product_img"];
	if (!empty($sku) && strpos($sku, "-")) {
		list($code, $item_no) = explode("-", $sku, 2);

		$n = rand(2, 5);
		$new_img_url = dirname($img_url) . "/" . basename($img_url, ".jpg") . "_" . $n . ".jpg";
		
		$data['error'] = $error;	
		$data['sku'] = $sku;
		$data['img_url'] = $new_img_url;
	} else {
		$data['error'] = "SKU not found!";
	}

	echo json_encode($data);
}
?>

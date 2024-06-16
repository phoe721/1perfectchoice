<?
require_once("class/validator.php");
$validator = new validator();
$data = array();
$error = "";
$count = 0;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["input"])) { 
	$input = $_POST["input"];
	$sku = $input;
	$img_url = $_POST["product_img"];
	if (!empty($sku) && strpos($sku, "-")) {
		list($code, $item_no) = explode("-", $sku, 2);

		begin:
		$n = rand(2, 5);
		$new_img_url = dirname($img_url) . "/" . basename($img_url, ".jpg") . "_" . $n . ".jpg";
			
		if ($validator->check_url($new_img_url)) {
			goto end;
		} else if ($count >= 3) {
			$new_img_url = $img_url;
		} else {
			$count++;
			goto begin;
		}

		end:
		$data['error'] = $error;	
		$data['sku'] = $sku;
		$data['img_url'] = $new_img_url;
	} else {
		$data['error'] = "SKU not found!";
	}

	echo json_encode($data);
}
?>

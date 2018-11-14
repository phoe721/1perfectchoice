<?
require_once("init.php");
require_once("class/validator.php");
$validator = new validator();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$url = IMAGE_SERVER . "$code/$item_no.jpg";
	if ($validator->check_url($url)) {
		$result = "<img src='$url' width='500px' alt='$sku'>";
	} else {
		$result = "<img src='' alt='Not Found'>";
	}

	echo json_encode($result);
}
?>

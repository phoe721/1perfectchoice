<?
require_once("init.php");
require_once("class/check_links.php");
$cl = new check_links();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sku"])) { 
	$sku = $_POST["sku"];
	list($code, $item_no) = explode("-", $sku, 2);
	$url = IMAGE_SERVER . "$code/$item_no.jpg";
	if ($cl->check_link($url)) {
		$result = "<img src='$url' alt='$sku'>";
	} else {
		$result = "<img src='' alt='Not Found'>";
	}

	echo json_encode($result);
}
?>

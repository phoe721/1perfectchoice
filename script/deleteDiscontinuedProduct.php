<?
require_once("class/product.php");
require_once("class/discontinued.php");
require_once("class/validator.php");
$product = new product();
$discontinued = new discontinued();

$list = $discontinued->get_list();
$total = count($list);
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($product->check_exist($code, $item_no)) {
		printf("Deleting product for $sku - It's discontinued!\n");
		$product->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in product table!\n");
	}
}
printf("Total: %d Removed: %d\n", $total, $removeCount);
?>

<?
require_once("class/ASIN.php");
require_once("class/costs.php");
require_once("class/dimensions.php");
require_once("class/packages.php");
require_once("class/product.php");
require_once("class/set_list.php");
require_once("class/UPC.php");
require_once("class/weights.php");
require_once("class/inventory.php");
require_once("class/manufacturing_country.php");
require_once("class/discontinued.php");
require_once("class/validator.php");
$ASIN = new ASIN();
$costs = new costs();
$dimensions = new dimensions();
$packages = new packages();
$product = new product();
$set_list = new set_list();
$UPC = new UPC();
$weights = new weights();
$inventory = new inventory();
$manufacturing_country = new manufacturing_country();
$discontinued = new discontinued();

$list = $discontinued->get_list();
$total = count($list);

/*
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($ASIN->check_exist($code, $item_no)) {
		printf("Deleting ASIN for $sku - It's discontinued!\n");
		$ASIN->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in ASIN table!\n");
	}
}
printf("Total: %d Removed: %d in ASIN table\n", $total, $removeCount);
*/

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($costs->check_exist($code, $item_no)) {
		printf("Deleting costs for $sku - It's discontinued!\n");
		$costs->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in costs table!\n");
	}
}
printf("Total: %d Removed: %d in costs table\n", $total, $removeCount);

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($dimensions->check_exist($code, $item_no)) {
		printf("Deleting dimensions for $sku - It's discontinued!\n");
		$dimensions->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in dimensions table!\n");
	}
}
printf("Total: %d Removed: %d in dimensions table\n", $total, $removeCount);

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($packages->check_exist($code, $item_no)) {
		printf("Deleting packages for $sku - It's discontinued!\n");
		$packages->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in packages table!\n");
	}
}
printf("Total: %d Removed: %d in packages table\n", $total, $removeCount);

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
printf("Total: %d Removed: %d in product table\n", $total, $removeCount);

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($set_list->check($code, $item_no)) {
		printf("Deleting set_list for $sku - It's discontinued!\n");
		$set_list->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in set_list table!\n");
	}
}
printf("Total: %d Removed: %d in set_list table\n", $total, $removeCount);

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($UPC->check_exist($code, $item_no)) {
		printf("Deleting UPC for $sku - It's discontinued!\n");
		$removeCount++;
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in UPC table!\n");
	}
}
printf("Total: %d Removed: %d in UPC table\n", $total, $removeCount);

$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($weights->check_exist($code, $item_no)) {
		printf("Deleting weights for $sku - It's discontinued!\n");
		$weights->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in weights table!\n");
	}
}
printf("Total: %d Removed: %d in weights table\n", $total, $removeCount);

/*
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($inventory->check_exist($code, $item_no)) {
		printf("Deleting inventory for $sku - It's discontinued!\n");
		$inventory->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in inventory table!\n");
	}
}
printf("Total: %d Removed: %d in inventory table\n", $total, $removeCount);
 */

/*
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($manufacturing_country->check_exist($code, $item_no)) {
		printf("Deleting manufacturing_country for $sku - It's discontinued!\n");
		$manufacturing_country->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in Manufacturing Country table!\n");
	}
}
printf("Total: %d Removed: %d in Manufacturing Country table\n", $total, $removeCount);
*/
?>

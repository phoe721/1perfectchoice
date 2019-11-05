<?
require_once("class/costs.php");
require_once("class/discontinued.php");
require_once("class/validator.php");
$costs = new costs();
$discontinued = new discontinued();

$list = $discontinued->get_list();
$total = count($list);
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku);
	if ($costs->check_exist($code, $item_no)) {
		printf("Deleting cost for $sku - It's discontinued!\n");
		$costs->delete($code, $item_no);
		$removeCount++;
	} else {
		//printf("Skip $sku - It's not found in costs table!\n");
	}
}
printf("Total: %d Removed: %d\n", $total, $removeCount);
?>

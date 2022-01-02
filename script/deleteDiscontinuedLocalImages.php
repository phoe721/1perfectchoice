<?
require_once("class/discontinued.php");
$discontinued = new discontinued();

$list = $discontinued->get_list();
$total = count($list);
$removeCount = 0;
foreach($list as $sku) {
	list($code, $item_no) = explode("-", $sku, 2);
	if ($code == "MO") {
		$file = IMG . "/$code/$item_no.jpg";
		if (file_exists($file)) {
			printf("Going to delete mage for $sku - It's discontinued!\n");
			printf("File path: $file\n");
			if(unlink($file)) {
				printf("Image for $sku is deleted!\n");
				$removeCount++;
			} else {
				printf("Image for $sku cannot be deleted!\n");
			}
		} else {
			// Do Nothing
		}
	}
}
printf("Total: %d Removed: %d\n", $total, $removeCount);
?>

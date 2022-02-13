<?
require_once("class/inventory.php");
$inventory = new inventory();

if ($inventory->truncate()) {
	printf("Inventory table truncated!\n");
} else {
	printf("Failed to truncate inventory table!\n");
}
?>

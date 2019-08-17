<?
require_once("../class/costs.php");
$costs = new costs();
$sku = "AC-90300";
list($code, $item_no) = explode("-", $sku);
for ($i = 0; $i < 10; $i++) {
$cost = $costs->get_cost($code, $item_no);
$unit = $costs->get_unit($code, $item_no);
$updated_time = $costs->get_updated_time($code, $item_no);
$result = "$sku\t$cost\t$unit\t$updated_time" . PHP_EOL;
echo $result;
}
?>

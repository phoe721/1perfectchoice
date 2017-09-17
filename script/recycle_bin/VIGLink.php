<?
/* Initialization */
require_once('init.php');
require_once('functions.php');
require_once('simple_html_dom.php');

/* Deinfe Constants */
define('SKU_LIST', AMAZON_UPLOAD . 'VIG_sku.txt');

$uid = uniqid();
prepare($uid);
$product['vendorCode'] = 'VIG';
$query = get_vendor_query_url();

$file = fopen(SKU_LIST, "r") or die("Unable to open file!");
$file2 = fopen($link_file, "a+") or die("Unable to open file!");
if ($file) {
	while (($sku = fgets($file)) != false) {
		$sku = trim($sku);
		$queryURL = $query . $sku; 
		$page = file_get_html("$queryURL");

		if (isset($page)) {
			$i = 0;
			foreach ($page->find('div.details-area small') as $item) {
				$item = strip_tags($item);
				if ($sku == $item) {
					$link = $page->find('a.product-image', $i)->href; 
					$output = "$sku\t$link\n";
					echo $output;
					fwrite($file2, $output);
				}	
				$i++;
			}
		}
	}
}
fclose($file);
fclose($file2);
?>

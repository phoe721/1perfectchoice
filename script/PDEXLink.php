<?
/* Helper Scripts */
require_once('init.php');
require_once('functions.php');
require_once('simple_html_dom.php');

/* Initialization */
define('INPUT', UPLOAD . 'sku.txt');
$product['vendor'] = 'Poundex';
set_vendor_code();
$query = get_vendor_query_url();
$catalogURL = "http://www.poundex.com/CatalogSite/";
$uid = uniqid();
prepare($uid);

// Cleanup first
file_put_contents($status_file, "");

// Read skus from file
$input = fopen(INPUT, "r") or die("Unable to open file!");
$output = fopen($status_file, "a+") or die("Unable to open file!");
if ($input) {
	while (($sku = fgets($input)) != false) {
		$sku = trim($sku);
		$queryURL = $query . $sku; 
		$page = file_get_html($queryURL);

		if ($page->find('div.productListingWrapper a', 0)) {
			$link = $page->find('div.productListingWrapper a', 0)->href;
			$link = $catalogURL . $link;
			echo "$sku, $link" . PHP_EOL;
			fwrite($output, "$sku,$link\n");
		}
	}
}
fclose($output);
fclose($input);

$input = fopen($status_file, "r") or die("Unable to open file!");
if ($input) {
	while (($line = fgets($input)) != false) {
		$line = trim($line);
		list($sku, $link) = explode(',', $line);
		$page = file_get_html($link);
		$imgURL = $page->find('#ctl00_cphContentHolder_imgATag', 0)->href;
		$imgURL = $catalogURL . $imgURL;
		echo "$sku, $imgURL" . PHP_EOL;

		$item = $product['vendorCode'] . "-" . $sku;
		$filename = $img_dir . $item . ".jpg";
		$result = download($imgURL, $filename);
	}
}
fclose($input);
?>

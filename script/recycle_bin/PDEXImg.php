<?
/* Initialization */
require_once('init.php');
require_once('functions.php');
require_once('simple_html_dom.php');
define("INPUT", "output.txt");

$product['vendor'] = 'Poundex';
$catalogURL = "http://www.poundex.com/CatalogSite/";
$uid = uniqid();
prepare($uid);

$file = fopen(INPUT, "r") or die("Unable to open file!");
if ($file) {
	while (($line = fgets($file)) != false) {
		$line = trim($line);
		list($sku, $link) = explode(",", $line);
		$page = file_get_html($link);
		$imgURL = $page->find('#ctl00_cphContentHolder_imgATag', 0)->href;
		$imgURL = $catalogURL . $imgURL;
		echo "$sku, $imgURL\n";

		$item = VENDOR . "-" . $sku;
		$filename = $img_dir . $item . ".jpg";
		$result = download($imgURL, $filename);
	}
}
?>

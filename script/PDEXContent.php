<?
/* Display Errors */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("INPUT", "output.txt");
define("OUTPUT", "content.txt");
define("VENDOR", "Poundex");
define("VENDOR_CODE", "PDEX-");

require_once('simple_html_dom.php');
$catalogURL = "http://www.poundex.com/CatalogSite/";

file_put_contents(OUTPUT, "");
$file = fopen(INPUT, "r") or die("Unable to open file!");
$file2 = fopen(OUTPUT, "a+") or die("Unable to open file!");
if ($file) {
	while (($line = fgets($file)) != false) {
		$line = trim($line);
		list($sku, $link) = explode(",", $line);
		$page = file_get_html($link);
		$name = $page->find('#ctl00_cphContentHolder_lblProductName', 0)->plaintext;
		$name = VENDOR . " " . trim($name);
		$description = $page->find('#ctl00_cphContentHolder_lblDescription', 0)->plaintext;
		$description = preg_replace("/\n.*/", "", $description);
		$description = preg_replace("/\&nbsp;/", "", $description);
		$description = trim($description);
		$sku = VENDOR_CODE . $sku;
		echo "$sku, $name, $description\n";

		fwrite($file2, "$sku\t$name\t$description\n");
	}
}

?>

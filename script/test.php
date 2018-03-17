<?	
/* Initialization */
require_once("functions.php");

/*
$input = UPLOAD . "input.txt";
$output = UPLOAD . "output.txt";
$handle = fopen($input, "r");
$handle2 = fopen($output, "w+");
if ($handle & $handle2) {
	while (($line = fgets($handle)) !== false) {
		$page = file_get_html($line);
		if (isset($page)) {
			$category = $sku = "";
			if ($page->find('div.prodName', 0)) {
				$category = $page->find('div.prodName', 0)->plaintext;
			}
			if ($page->find('div.prodNumber', 0)) {
				$sku = $page->find('div.prodNumber', 0)->plaintext;
			}
			$output = "$category\t$sku" . PHP_EOL;
			fwrite($handle2, $output);
			sleep(3);
		}
		$page->clear();
	}
	fclose($handle2);
	fclose($handle);
} else {
	echo "Failed to open $input";
}
*/

$url = "https://www.flatfair.com/bedroom-furniture.html";
$page = file_get_html($url);
$links = array();
if (isset($page)) {
	if ($page->find('a.product-item-link')) {
		foreach($page->find('a.product-item-link') as $i=>$link) {
			if (!empty($link->href)) {
				echo "Found link: " . trim($link->href) . PHP_EOL;
				$links[$i] = trim($link->href);
			}
		}
	}
	$page->clear();
}


?>

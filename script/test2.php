<?	
/* Initialization */
require_once("functions.php");

$input = UPLOAD . "links2.txt";
$output = UPLOAD. "output2.txt";
$handle = fopen($input, "r"); 
$handle2 = fopen($output, "a+");
$links = array();
$count = 0;
if ($handle && $handle2) {
	while (!feof($handle)) {
		$url = trim(fgets($handle));
		if (!empty($url)) {
			$page = file_get_html($url);
			if (isset($page) && !empty($page)) {
				if ($page->find('div.product-img-box a')) {
					$link = $page->find('div.product-img-box a', 0);
					if (!empty($link->href)) {
						echo "Found link:" . trim($link->href) . PHP_EOL;
						$links[$count] = trim($link->href);
						$count++;
					}
				}
				$page->clear();
			}
		}
	}

	for ($i = 0; $i < count($links); $i++) {
		echo "Processing page: " .  $links[$i] . PHP_EOL;
		$page2 = file_get_html($links[$i]);
		if (isset($page2)) {
			if ($page2->find('div.product-sku', 0)) {
				$sku = $page2->find('div.product-sku', 0)->plaintext;
				$sku = filter($sku);
			}
			if ($page2->find('p.product-image a')) {
				$img_url = $page2->find('p.product-image a', 0)->href;
			}
			$result = $sku . "\t" . $img_url . PHP_EOL;
			fwrite($handle2, $result);
			echo "Finish Processing page: " . $links[$i] . PHP_EOL;
		}
		$page2->clear();
	}

	fclose($handle2);
	fclose($handle);
}

function filter($str) {
	$str = preg_replace('/' . PHP_EOL . '/', ' ', $str);
	$str = preg_replace('/\&nbsp\;/', '', $str);	// Remove &nbps;
	$str = preg_replace('/\&amp\;/', '', $str);		// Remove &amp;
	$str = preg_replace('/\(\d+\)/', '', $str);		// Remove (numbers) 
	$str = preg_replace('/w\//', 'with ', $str);	// Replace "w/" with "with "
	$str = preg_replace('/Drw/', 'Drawer ', $str);	// Replace "Drw" with "Drawer "
	$str = preg_replace('/\$/', '', $str);			// Remove $
	$str = preg_replace('/\*/', '', $str);			// Remove *
	$str = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $str);
	$str = preg_replace('/[A-Z0-9]{2,5}-[A-Z0-9]{3,15}{-}*[A-Z0-9]*/','', $str);
	$str = preg_replace('/\s\s+/', ' ', $str);		// Remove extra spaces
	$str = strip_tags($str);	// Strip HTML tags
	$str = trim($str);			// Trim spaces

	return $str;
}
?>

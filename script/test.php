<?	
/* Initialization */
require_once('functions.php');
/*
$result = $db->query("SELECT sku FROM product");
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo $row["sku"] . PHP_EOL;
	}
}
*/

/*
foreach ($dsc as $sku) {
	//echo $sku . PHP_EOL;
	$result = $db->query("DELETE FROM product WHERE sku = '" . $sku . "'");
	if ($result) {
		echo "$sku is removed" . PHP_EOL;
	} else {
		echo "$sku is not removed" . PHP_EOL;
	}
}

$input_file = UPLOAD . "input.txt";
$output_file = DOWNLOAD . "output.txt";
$file = fopen($input_file, 'r');
$file2 = fopen($output_file, 'w+');
if ($file && $file2) {
	while (($line = fgets($file)) !== false) {
		$sku = trim($line);
		$result = $db->query("SELECT sku, item_type FROM product WHERE sku = '" . $sku . "'");
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$output = $row["sku"] . "\t" . $row["item_type"] . PHP_EOL;
			echo $output;
			fwrite($file2, $output);
		} else {
			$output = $sku . PHP_EOL;
			echo $output;
			fwrite($file2, $output);
		}
	}
}
fclose($file);
fclose($file2);
*/

/*
	// Grab links from page
	$page = 108;
	for ($j = 108; $j < 1779; $j+=36) {
		$url = "https://www.houzz.com/photos/furniture/query/ACME/nqrw/p/" . $j;
		echo "Getting links from $url" . PHP_EOL;
		$str = file_get_contents($url);
		preg_match_all("/\"https:\/\/www.houzz.com\/photos\/[0-9]{8}\/[A-Za-z0-9-]*\"/", $str, $matches);
		$links = implode("\n", $matches[0]);
		$file = "test";
		$handle = fopen("test", "a+");
		if ($handle) {
			fwrite($handle, $links);
		}
	}
*/

	$file = fopen("input", "r");
	$file2 = fopen("output", "a+");
	if ($file) {
		while (($line = fgets($file)) != false) {
			echo "Processing page: " .  $line . PHP_EOL;
			$page = file_get_html($line);
			$text = $sku = $title = $img_url = $description = $size_weight = $width = $depth = $height = $material = $category = "";
			if (isset($page)) {
				$text = implode(" ", $page->find("script"));
				if(preg_match("/\"sku\":\"[A-Z0-9]+\"/", $text, $found)) {
					$part = explode(":", $found[0]); 
					$sku = preg_replace("/\"/", "", $part[1]);
				}
	
				$title = filter2($page->find("h1.header-1", 0)->plaintext);
				$img_url = filter2($page->find("img#mainImage", 0)->src);
				$description = filter2($page->find("div.description", 0));
		
				$size_weight = $width = $depth = $height = $weight = $material = $category = "";
				foreach($page->find("dt.key") as $e) {
					$key = $e->plaintext;
					if ($key == "Size/Weight") {
						$size_weight = filter2(preg_replace("/\"/", "", $e->next_sibling()->plaintext));
						$part = explode("/", $size_weight);
						$width = filter2(preg_replace("/W/", "", $part[0]));
						$depth = filter2(preg_replace("/D/", "", $part[1]));
						$height = filter2(preg_replace("/H/", "", $part[2]));
						$weight = preg_replace("/lb\./", "", $part[3]);
						$weight = filter2(preg_replace("/oz\./", "", $weight));
					} else if ($key == "Materials") {
						$material = filter2($e->next_sibling()->plaintext);
					} else if ($key == "Category") {
						$category = filter2($e->next_sibling()->plaintext);
					}
		
				}

				$output = "$sku\t$title\t$img_url\t$description\t$width\t$depth\t$height\t$weight\t$material\t$category" . PHP_EOL;
				fwrite($file2, $output);
			}
			$page->clear();
		}
		fclose($file);
		fclose($file2);
	}

function filter2($str) {
	$new_str = strip_tags($str);
	$new_str = preg_replace("/\s+/", " ", $new_str);
	$new_str = preg_replace("/\t/", " ", $new_str);
	$new_str = preg_replace("/\&nbsp;/", "", $new_str);
	$new_str = trim($new_str);

	return $new_str;
}
?>

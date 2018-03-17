<?
/* Initialization */
require_once('functions.php');

/* Start of Program */
if (isset($_POST['url']) && isset($_POST['uid'])) {
	global $uid, $url, $result_file;
	$url = $_POST['url'];
	$uid = $_POST['uid'];
	prepare($uid);

	// Process link
	log_status("Processing page: " . $url);
	$page = file_get_html($url);
	if (isset($page)) {
		process_page();
		log_status("Finish Processing page: " . $url);
	}
	$page->clear();

	log_link_file($result_file);
	log_status("Done");
}

function process_page() {
	reset_product_array();
	get_fields();
	get_title();
	get_description();
	get_features();
	get_price();
	get_keywords();
	output_product_str();
}

function get_title() {
	global $page, $product;
	$tmp = $page->find('h1.page-title', 0)->plaintext;
	$tmp = remove_brand($tmp);
	$tmp = remove_mpn($tmp);
	$product['Title'] = filter($tmp);
}

function get_features() {
	global $page, $product;
	$tmp = $page->find('div.overview', 0)->plaintext;
	$product['Features'] = filter($tmp);
}

function get_description() {
	global $page, $product;
	$tmp = $page->find('div.description', 0)->plaintext;
	$tmp = preg_replace('/[^.]\b0+/', '', $tmp);
	$tmp = preg_replace('/:/', '', $tmp);
	$tmp = preg_replace('/Includes/', '. Includes: ', $tmp);
	$tmp = preg_replace('/Dimensions/', '. Dimensions: ', $tmp);
	$tmp = remove_brand($tmp);
	$tmp = remove_mpn($tmp);
	$product['Description'] = filter($tmp);
}

function get_fields() {
	global $page, $product;
	if ($page->find('table.data', 0)) {
		$tmp = trim(preg_replace('/\s\s+/', ' ', $page->find('table.data', 0)->plaintext));
		$data = explode(' ', trim($tmp));
		for ($i = 0; $i < count($data); $i++) {
			switch($data[$i]) {
				case 'MPN':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Brand':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Length':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Width':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Height':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Material':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Finish':
						$product[$data[$i]] = $data[$i+1];
						break;
				case 'Color':
						$product[$data[$i]] = $data[$i+1];
						break;
			}
		}
	}
}

function get_price() {
	global $page, $product;
	$tmp = $page->find('div.price-final_price', 0)->plaintext;
	$product['Price'] = filter($tmp);
}

function get_keywords() {
	global $page, $product;
	$tmp = $page->find('meta[name=keywords]', 0)->getAttribute('content');
	$product['Keywords'] = filter($tmp);
}

function reset_product_array() {
	global $product;
	$product = array();
}

function output_product_str() {
	global $product, $result_file;
	$file = fopen($result_file, 'a+');
	if ($file) {
		$productStr = implode("\t", $product);
		$productStr .= PHP_EOL;
		fwrite($file, $productStr);
	}
	fclose($file);
}

function remove_brand($str) {
	global $product;
	if (isset($product['Brand'])) {
		$str = preg_replace('/' . $product['Brand'] . ' /', ' ', $str);
	}

	return $str;
}

function remove_mpn($str) {
	global $product;
	if (isset($product['MPN'])) {
		$str = preg_replace('/' . $product['MPN'] . ' /', ' ', $str);
	}

	return $str;
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

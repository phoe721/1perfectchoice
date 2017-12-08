<?
/* Connect to Database */
require_once('init.php');
require_once('database.php');
require_once('simple_html_dom.php');

$page = $uid = $data_file = $link_file = $result_file = $status_file = $img_dir = $img_zip = '';
$product = $result = $match = array();

$db	= new database;
$con = $db->connect("localhost", "root", "revive", "1perfectchoice");
mysqli_set_charset($con, "utf8");

$debug = false;

function prepare($id) {
	global $uid, $data_file, $link_file, $result_file, $status_file, $img_dir, $img_zip, $debug;
	$uid = $id;
	$user_upload = UPLOAD . $uid . '/';
	$user_download = DOWNLOAD . $uid . '/';
	if (empty($data_file)) $data_file = $user_download . 'data.txt';
	if (empty($link_file)) $link_file = $user_download . 'links.txt';
	if (empty($result_file)) $result_file = $user_download . 'result.txt';
	if (empty($img_dir)) $img_dir = $user_download . 'img/';
	if (empty($img_zip)) $img_zip = $user_download . 'images.zip';
	if (empty($status_file)) $status_file = $user_download . 'status';
	if (!is_dir($user_upload)) mkdir($user_upload, 0777, true);
	if (!is_dir($user_download)) mkdir($user_download, 0777, true);
	if (!is_dir($img_dir)) mkdir($img_dir, 0777, true);
}

function get_info() {
	reset_product_array();
	get_fields();
	cleanup();
	set_skus();
	set_vendor_code();
	set_sku_str();
	set_brand();
	set_name();
	set_type();
	set_price();
	set_features();
	set_description();
	set_keywords();
	set_main_image();
	output_product_str();
}

function set_page($thisPage) {
	global $page;
	$page = $thisPage;
}

function reset_product_array() {
	global $product;
	$product = array();
}

function set_skus() {
	global $page, $product, $debug;
	$desc = $page->find('div.std', 1)->plaintext;
	$product['skus'] = array();
	if (preg_match('/-SE.*/', $product['sku'])) { 
		$product['sku'] = preg_replace('/-SE.*/', '', $product['sku']);
		if (preg_match_all('/\(\d+\)/', $desc, $found)) {
			foreach($found[0] as $key => $value) {
				$value = preg_replace('/\(/', '', $value);
				$value = preg_replace('/\)/', '', $value);
				$product['skus'][$key] = $value;
			}
		} else {
			$product['skus'][0] = $product['sku'];
		}
	} else if (preg_match('/\d+-\d+/', $product['sku'])) {
		$product['skus'] = explode("-", $product['sku']);
	} else {
		$product['skus'][0] = $product['sku'];
	}

	if ($debug) logger("Set product SKU: " . $product['sku']);
}

function set_sku_str() {
	global $product, $debug;
	foreach ($product['skus'] as $key => $value) {
		if ($key == 0) {
			$product['skuStr'] = $value;
		} else {
			$product['skuStr'] .= '-' . substr($value, -2);			
		}
	}

	if (!preg_match('/' . $product['vendorCode'] . '/', $product['skuStr'])) {
		$product['skuStr'] = $product['vendorCode'] . '-' . $product['skuStr'];
	}

	if ($debug) logger("Set product SKU string: " . $product['skuStr']);
}

function get_fields() {
	global $page, $product;
	if ($page->find('table.data', 0)) {
		$tmp = preg_replace('/\s\s+/', ' ', $page->find('table.data', 0)->plaintext);
		$data = explode(' ', trim($tmp));
		for ($i = 0; $i < count($data); $i++) {
			switch($data[$i]) {
				case 'MPN':
						$product['sku'] = str_pad($data[$i+1], 5, "0", STR_PAD_LEFT);
						break;
				case 'Brand':
						$product['vendor'] = '';
						for ($j = $i+1; $data[$j] != "Collection"; $j++) {
							$product['vendor'] .= $data[$j] . ' ';
						}
						$product['vendor'] = trim($product['vendor']);
						break;
				case 'Length':
						$product['length'] = $data[$i+1];
						break;
				case 'Width':
						$product['width'] = $data[$i+1];
						break;
				case 'Height':
						$product['height'] = $data[$i+1];
						break;
				case 'Material':
						$product['material'] = '';
						for ($j = $i+1; isset($data[$j]) && $data[$j] != "Finish" && $data[$j] != "Color" && $data[$j] != "Shipping"; $j++) {
							$product['material'] .= $data[$j] . ' ';
						}
						$product['material'] = trim($product['material']);
						break;
				case 'Finish':
						$product['color'] = $data[$i+1];
						break;
				case 'Color':
						$product['color'] = $data[$i+1];
						break;
				case 'Shipping':
						$product['freeShipping'] = $data[$i+1];
						if($product['freeShipping'] == 'Yes') {
							$product['shipping'] = 0;
						} else {
							$product['shipping'] = '';
						}
						break;
			}
		}
	}
}

function cleanup() {
	global $page, $product;
	// Coaster - lookup sku
	if (empty($product['sku']) && $page->find('title')) {
		$title = $page->find('title', 0)->plaintext;
		if (preg_match('/coaster/i', $title)) {
			$product['vendor'] = 'Coaster';
			$pieces = explode(' ', $title);
			foreach($pieces as $word) {
				if (preg_match('/[A-Z0-9]{4,7}/', $word)) {
					$product['sku'] = $word;
					break;
				}
			}
		}
	}

	if (!isset($product['sku'])) $product['sku'] = uniqid();
	if (!isset($product['vendor'])) $product['vendor'] = '';
	if (!isset($product['color']) || $product['color'] == 'N/A' || $product['color'] == 'No') $product['color'] = '';
	if (!isset($product['material']) || $product['material'] == 'N/A' || $product['material'] == 'No') $product['material'] = '';
	if (!isset($product['length']) || $product['length'] == 'N/A' || $product['length'] == 'No') $product['length'] = '';
	if (!isset($product['width']) || $product['width'] == 'N/A' || $product['width'] == 'No') $product['width'] = '';
	if (!isset($product['height']) || $product['height'] == 'N/A' || $product['height'] == 'No') $product['height'] = '';
}

function set_brand() {
	global $product, $debug;
	$product['brand'] = BRAND;

	if ($debug) logger("Set product brand " . $product['brand']);
}

function set_name() {
	global $page, $product, $debug;
	$tmp = $page->find('span.h1', 0)->plaintext;
	$tmp = filter_sku_vendor($tmp);
	$tmp = filter($tmp);
	$product['name'] = BRAND . " " . $tmp;

	if ($debug) logger("Set product name: " . $product['name']);
}

function set_type() {
	global $product, $debug;

	if (preg_match('/sofa bed/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/coffee table/i', $product['name'])) {
		$product['type'] = "coffee-tables";
	} else if (preg_match('/occasional set/i', $product['name'])) {
		$product['type'] = "coffee-tables";
	} else if (preg_match('/end table/i', $product['name'])) {
		$product['type'] = "end-tables";
	} else if (preg_match('/console table/i', $product['name'])) {
		$product['type'] = "sofa-tables";
	} else if (preg_match('/sofa table/i', $product['name'])) {
		$product['type'] = "sofa-tables";
	} else if (preg_match('/corner table/i', $product['name'])) {
		$product['type'] = "sofa-tables";
	} else if (preg_match('/accent table/i', $product['name'])) {
		$product['type'] = "sofa-tables";
	} else if (preg_match('/sofa/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/loveseat/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/love seat/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/recliner/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/futon/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/settee/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/sectional/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/office chair/i', $product['name'])) {
		$product['type'] = "adjustable-home-desk-chairs";
	} else if (preg_match('/rocking chair/i', $product['name'])) {
		$product['type'] = "nursery-rocking-chairs";
	} else if (preg_match('/accent chair/i', $product['name'])) {
		$product['type'] = "living-room-chairs";
	} else if (preg_match('/chaise/i', $product['name'])) {
		$product['type'] = "living-room-chaise-lounges";
	} else if (preg_match('/chair/i', $product['name'])) {
		$product['type'] = "standard-sofas";
	} else if (preg_match('/bar table/i', $product['name'])) {
		$product['type'] = "bar-tables";
	} else if (preg_match('/stool/i', $product['name'])) {
		$product['type'] = "barstools";
	} else if (preg_match('/tv stand/i', $product['name'])) {
		$product['type'] = "television-stands";
	} else if (preg_match('/media tower/i', $product['name'])) {
		$product['type'] = "audio-video-media-cabinets";
	} else if (preg_match('/tv console/i', $product['name'])) {
		$product['type'] = "home-entertainment-centers";
	} else if (preg_match('/entertainment center/i', $product['name'])) {
		$product['type'] = "home-entertainment-centers";
	} else if (preg_match('/computer desk/i', $product['name'])) {
		$product['type'] = "computer-desks";
	} else if (preg_match('/storage bench/i', $product['name'])) {
		$product['type'] = "storage-benches";
	} else if (preg_match('/headboard/i', $product['name'])) {
		$product['type'] = "headboards";
	} else if (preg_match('/ottoman/i', $product['name'])) {
		$product['type'] = "storage-ottomans";
	} else if (preg_match('/fireplace/i', $product['name'])) {
		$product['type'] = "ventless-fireplaces";
	} else if (preg_match('/dining table/i', $product['name'])) {
		$product['type'] = "dining-tables";
	} else if (preg_match('/side chair/i', $product['name'])) {
		$product['type'] = "dining-chairs";
	} else if (preg_match('/bed/i', $product['name'])) {
		$product['type'] = "beds";
	} else {
		$product['type'] = "";
	}

	if ($debug) logger("Set product type: " . $product['type']);
}

function set_price() {
	global $page, $product, $debug;
	$tmp = $page->find('div.price-info', 0)->plaintext;
	$tmp = filter($tmp);
	if (preg_match('/Special Price/', $tmp)) {
		$data = explode(' ', trim($tmp));
		$product['price'] = trim(preg_replace('/\$/', '', $data[5]));
	} else {
		$product['price'] = trim(preg_replace('/\$/', '', $tmp));
	}

	if ($debug) logger("Set product price: " . $product['price']);
}

function set_features() {
	global $page, $product, $debug;
	$tmp = $page->find('div.short-description', 0)->plaintext;
	$product['feature'] = explode(PHP_EOL, $tmp);
	foreach($product['feature'] as $index => $value) {
		if (!empty($value)) {
			$product['feature'][$index] = filter($value);
		} else {
			unset($product['feature'][$index]);
		}
	}

	$featureMax = 5;
	$featureCount = count($product['feature']);
	if ($featureCount > $featureMax) {
		for($i = ($featureCount - 1); $i > ($featureMax - 1); $i--) {
			unset($product['feature'][$i]);
		}
	} else {
		for ($i = $featureCount; $i < $featureMax; $i++) {
			$product['feature'][$i] = '';
		}
	}
	$product['features'] = implode("\t", $product['feature']);	

	if ($debug) logger("Set product feature: " . $product['features']);
}

function set_description() {
	global $page, $product, $debug;
	$tmp = $page->find('div.std', 1)->plaintext;
	$tmp = preg_replace('/[^.]\b0+/', '', $tmp);
	$tmp = preg_replace('/:/', '', $tmp);
	$tmp = filter($tmp);
	$tmp = filter_sku_vendor($tmp);
	$tmp = preg_replace('/Includes/', '<br>Includes: ', $tmp);
	$tmp = preg_replace('/Dimensions/', '<br>Dimensions: ', $tmp);
	$product['description'] = trim($tmp);

	if ($debug) logger("Set product description: " . $product['description']);
}

function set_keywords() {
	global $page, $product, $debug;
	$tmp = $page->find('meta[name=keywords]', 0)->getAttribute('content');
	$tmp = filter_sku_vendor($tmp);
	$tmp = filter_bad_keyword($tmp);
	$tmp = filter($tmp);
	$product['keyword'] = explode(' ', $tmp);

	$keywordMax = 5;
	$keywordCount = count($product['keyword']);
	if ($keywordCount > $keywordMax) {
		unset($product['keyword'][0]);
	} else {
		for ($i = $keywordCount; $i < $keywordMax; $i++) {
			$product['keyword'][$i] = '';
		}
	}
	$product['keyword'] = array_unique($product['keyword']);
	$product['keywords'] = preg_replace('/,$/', '', strtolower(implode(",", $product['keyword'])));

	if ($debug) logger("Set product keywords: " . $product['keywords']);
}

// Create queue to run later
function create_queue($uid, $command) {
	global $db;
	$result = $db->query("INSERT INTO queue (command, status, insert_time, update_time) VALUES ('$command', '0', NOW(), NOW())");
	if ($result) {
		$last_id = $db->last_insert_id();
		log_status("Queue created, your queue number is $last_id!");
	} else {
		$last_id = null;
		log_status("Failed to create queue!");
	}

	return $last_id;
}

// Move uploaded file
function move_file($uid, $file, $destination) {
	$tmp = $file["tmp_name"];
	move_uploaded_file($tmp, $destination) ;
}

function set_vendor_code() {
	global $db, $product, $debug;
	$result = $db->query("SELECT code FROM vendor WHERE name = '" . $product['vendor'] . "'");
	if (mysqli_num_rows($result) == 0) {
		$product['vendorCode'] = '';
	} else {
		$row = mysqli_fetch_array($result);
		$product['vendorCode'] = $row['code'];
	}

	if ($debug) logger("Set vendor code: " . $product['vendorCode']);
}

function get_vendor_query_url() {
	global $db, $product, $debug;
	if (isset($product['vendorCode'])) {
		$result = $db->query("SELECT queryURL FROM vendor WHERE code = '" . $product['vendorCode'] . "'");
	} else if (isset($product['vendor'])) {
		$result = $db->query("SELECT queryURL FROM vendor WHERE name = '" . $product['vendor'] . "'");
	}

	if (isset($result)) {
		if (mysqli_num_rows($result) == 0) {
			if ($debug) logger("[ERROR] Vendor query URL not found!");
			return null;
		} else {
			$row = mysqli_fetch_array($result);
			if ($debug) logger("Vendor query URL: " . $row['queryURL']);
			return $row['queryURL'];
		}
	}
}

function filter_sku_vendor($str) {
	global $product;
	$str = preg_replace('/' . $product['vendor'] . '/', '', $str);
	foreach($product['skus'] as $key => $val) {
		$val2 = preg_replace('/\b0+/', '', $val);
		$str = preg_replace('/' . $val . '/', '', $str);
		$str = preg_replace('/' . $val2 . '/', '', $str);
	}

	return $str;
}

function filter_sku_vendor_code($sku) {
	global $db, $debug;
	$parts = explode("-", $sku);
	if (isset($parts[0]) && (strlen($parts[0]) == 2)) {
		$result = $db->query("SELECT code FROM vendor WHERE code = '" . $parts[0] . "'");
		if ($result) {
			$newSKU = preg_replace('/' . $parts[0] . '-/', '', $sku);
			logger("SKU $sku has been filtered for vendor code to $newSKU!");
			return $newSKU;
		}
	}

	if ($debug) logger("SKU $sku has no vendor code!");
	return $sku;
}

function filter_bad_keyword($str) {
	$badKeywords = array('\d+',',','\dpcs','occasional','christmas','thanksgiving','holiday','on sales','discount','%','cheap','price','match','lower','top','and','with','left','right','light','dark','set','sides','pieces','side','pack','bonded','promotion','pcs','antique','metal','sectional','inch','los','angeles','furniture','store','local','like','\+','\&','the');

	foreach($badKeywords as $word) {
		$str = preg_replace('/' . $word . '/i', '', $str);
	}

	// Check if it's valid English word
	$pspell_link = pspell_new("en");
	$newStr = "";
	$pieces = explode(" ", $str);
	foreach($pieces as $word) {
		if (pspell_check($pspell_link, $word)) {
			$newStr .= $word . " ";
		}
	}

	return $newStr;
}

function filter($str) {
	$str = preg_replace('/' . PHP_EOL . '/', ' ', $str);
	$str = preg_replace('/\&nbsp\;/', '', $str);	// Remove &nbps;
	$str = preg_replace('/\&amp\;/', '', $str);		// Remove &amp;
	$str = preg_replace('/\s\s+/', ' ', $str);		// Remove extra spaces
	$str = preg_replace('/\(\d+\)/', '', $str);		// Remove (numbers) 
	$str = preg_replace('/w\//', 'with ', $str);	// Replace "w/" with "with "
	$str = preg_replace('/Drw/', 'Drawer ', $str);	// Replace "Drw" with "Drawer "
	$str = preg_replace('/\$/', '', $str);			// Remove $
	$str = preg_replace('/\*/', '', $str);			// Remove *
	$str = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $str);
	$str = preg_replace('/[A-Z0-9]{2,5}-[A-Z0-9]{3,15}{-}*[A-Z0-9]*/','', $str);
	$str = strip_tags($str);	// Strip HTML tags
	$str = trim($str);			// Trim spaces

	return $str;
}

function check_set_sku($sku) {
	global $debug;
	$parts = explode("-", $sku);
	if (count($parts) > 0) {
		foreach ($parts as $key => $val) $skus[$key] = $val;
		$skuLen = strlen($skus[0]);
		for ($i = 1; $i < count($skus); $i++) {
			if (strlen($skus[$i]) < $skuLen) {
				$length = $skuLen - strlen($skus[$i]);
				$skus[$i] = substr($skus[0], 0, $length) . $skus[$i]; 
			}
		}

		logger("SKU $sku is a set SKU: " . implode(", ", $skus));
		return $skus;
	} else {
		logger("SKU $sku is not a set SKU!");
		return false;
	}
}

function truncate_inventory() {
	global $db, $debug;
	$result = $db->query("TRUNCATE TABLE inventory");
	if ($result) {
		logger("Inventory table truncated!");
	} else {
		logger("Failed to truncate inventory table!");
	}
}

function update_inventory_by_file($filePath) {
	global $db, $debug;
	//$result = $db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE inventory LINES TERMINATED BY '\r\n'");
	$result = $db->query("LOAD DATA LOCAL INFILE '$filePath' INTO TABLE inventory");
	if ($result) {
		logger("Inventory updated with $filePath!");
	} else {
		logger("Failed to update inventory with $filePath!");
	}
}

function inventory_record_count() {
	global $db, $debug;
	$result = $db->query("SELECT COUNT(*) FROM inventory");
	$row = $result->fetch_row();
	$count = $row[0];
	return $count;
}

function set_main_image() {
	global $img_dir, $page, $product, $debug;
	if ($page->find('#image-main img', 0)) {
		$imgURL = $page->find('#image-main img', 0)->src;
		$product['mainImage'] = $imgURL;

		if ($debug) logger("Get product image: " . $imgURL);
	} else {
		if ($debug) logger("[ERROR] Fail to get product image: " . $product['sku']);
	}
}

function download($url, $path) {
	global $debug;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);

	$file = fopen($path, "w+");
	if ($file) {
		fputs($file, $data);
	} else {
		if ($debug) logger("[ERROR] Could not save $file to $path");
	}
	fclose($file);

	return file_exists($path);
}

function fetch_page($url, $timeout) {
	global $debug;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	$data = curl_exec($ch);

	if (!curl_errno($ch)) {
		return $data;
	} else {
		echo 'Error: ' . curl_error($ch);
	}

	curl_close($ch);
}

function progress($download_size, $downloaded, $upload_size, $uploaded) {
	if ($download_size > 0) {
		$percentage = round(($downloaded / $download_size * 100), 0);
		//if (($percentage % 10) == 0) echo $percentage . ".";
	}
}

function zip_images() {
	global $img_dir, $img_zip, $result, $debug;
	$zip = new ZipArchive();

	if (file_exists($img_zip)) {
		unlink($img_zip);
	}

	if ($zip->open($img_zip, ZipArchive::CREATE)) {
		$files = scandir($img_dir);
		foreach($files as $file) {
			if (preg_match('/.*\.jpg/', $file)) {
				$path = $img_dir . $file;
				$zip->addFile($path, $file);
			}
		}
	} else {
		if ($debug) logger("[ERROR] Could not open archive");
	}


	$zip->close();
}

function output_product_str() {
	global $product, $data_file, $result, $debug;
	$file = fopen($data_file, 'a+');
	if ($file) {
		$productStr = $product['type'] . "\t" . $product['sku'] . "\t" . $product['vendor'] . "\t" . $product['skuStr'] . "\t";
	   	$productStr .= $product['brand'] . "\t" . $product['name'] . "\t" . $product['price'] . "\t"; 
		$productStr .= $product['description'] . "\t" . $product['features'] . "\t" . $product['keywords'] . "\t"; 
		$productStr .= $product['color'] . "\t" . $product['material'] . "\t";
		$productStr .= $product['length'] . "\t" . $product['width'] . "\t" . $product['height'] . "\t"; 
		$productStr .= $product['mainImage'] . "\t" . $product['shipping'] . PHP_EOL;
		fwrite($file, $productStr);
		
		if ($debug) logger("Product info: " . $productStr);
	} else {
		if ($debug) logger("[ERROR] Could not save $file to $path");
	}
	fclose($file);
}

function grab_img($sku, $url) {
	global $img_dir;
	logger("Found link: " . $url);
	$img_path = $img_dir . $sku . ".jpg";
	if (file_exists($img_path)) $img_path = $img_dir . $sku . "-2.jpg";

	$result = download($url, $img_path);
	if ($result) {
		return $img_path;
	} else {
		return null;
	}
}

function grab_img_by_tag($sku, $url, $tag) {
	global $img_dir;
	$html = file_get_html($url);
	$img_url = $html->find($tag, 0)->src;
	if (!is_null($img_url)) {
		logger("Found link: " . $img_url);
		$img_path = $img_dir . $sku . ".jpg";
		if (file_exists($img_path)) $img_path = $img_dir . $sku . "-2.jpg";

		$result = download($img_url, $img_path);
		if ($result) {
			return $img_path;
		} else {
			return null;
		}
	}
	$html->clear();
}

// Amazon Only
function grab_amazon_img($sku, $url) {
	global $img_dir;
	$data = fetch_page($url, 5);
	$html = str_get_html($data);
	if (!empty($html)) {
		$img_url = $html->getElementById("landingImage")->getAttribute('data-old-hires');
		if (!empty($img_url)) {
			logger("Found link: " . $img_url);
			$img_path = $img_dir . $sku . ".jpg";
			$result = download($img_url, $img_path);
			if ($result) {
				return $img_path;
			} else {
				return null;
			}
		}
		$html->clear();
	} else {
		return null;
	}
}

function compare_images($path1, $path2) {
	global $img_dir;
	$image1 = new imagick();
	$image2 = new imagick();
	$image1->SetOption('fuzz', '5%');
	$image1->readImage($path1);
	$image2->readImage($path2);
	$d1 = $image1->getImageGeometry();
	$d2 = $image2->getImageGeometry();

	if (($d1['width'] == $d2['width']) && ($d1['height'] == $d2['height'])) {
		$result = $image1->compareImages($image2, 1);
		return $result[1];
	} else {
		$image1->scaleImage($d2['width'], $d2['height'], false); 
		$image1->writeImage($path1);
		$image1->destroy();
		$image1->SetOption('fuzz', '5%');
		$image1->readImage($path1);
		$result = $image1->compareImages($image2, 1);
		//$result[0]->writeImage(dirname($path1) . '/' . basename($path1, ".jpg") . "-3.jpg");
		if ($result[1] > 1000) {
			return 0;
		} else {
			return 1;
		}
	}
}

function upload_img_dir($server, $user, $pass, $directory, $img_dir) {
	$conn = ftp_connect($server) or die("Couldn't connect to $server");
	if (@ftp_login($conn, $user, $pass)) {
		ftp_pasv($conn, true); // Turn Passive Mode On
		$files = array_diff(scandir($img_dir), array('..', '.'));
		foreach ($files as $file) {
			$remote_file = "/images/" . $directory . "/" . $file;
			$img_file = $img_dir . $file;
			if (ftp_put($conn, $remote_file, $img_file, FTP_BINARY)) {
				log_result("Uploaded $file");
			} else {
				log_result("Failed to upload $file");
			}
		}
	} else {
		log_result("Failed to login FTP server!");
	}
	ftp_close($conn);
}

function get_type_sears($type) {
	$found = "";	
	switch($type) {
		case "sofas":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Sofas & Loveseats";
				break;
		case "headboards":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Headboards";
				break;
		case "dressers":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Dressers & Chests";
				break;
		case "chests of drawers":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Dressers & Chests";
				break;
		case "living room chaise lounges":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Chairs & Recliners";
				break;
		case "storage ottomans":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Ottomans";
				break;
		case "dining tables":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Tables";
				break;
		case "sideboards":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Carts & Islands";
				break;
		case "standing shelf units":
				$found = "Bed Bath & Home|Furniture|Storage Furniture|Shelves & Cabinets";
				break;
		case "storage cabinets":
				$found = "Bed Bath & Home|Furniture|Storage Furniture|Shelves & Cabinets";
				break;
		case "television stands":
				$found = "Bed Bath & Home|Furniture|Media Room Furniture|Entertainment Centers & TV Stands";
				break;
		case "end tables":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Living Room Tables";
				break;
		case "beds":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Beds";
				break;
		case "nightstands":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Nightstands";
				break;
		case "office desks":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Desks";
				break;
		case "home entertainment centers":
				$found = "Bed Bath & Home|Furniture|Media Room Furniture|Entertainment Centers & TV Stands";
				break;
		case "sofa tables":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Living Room Tables";
				break;
		case "lateral file cabinets":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Filing Cabinets";
				break;
		case "coffee tables":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Living Room Tables";
				break;
		case "ventless fireplaces":
				$found = "Bed Bath & Home|Furniture|Accent Furniture|Fireplaces";
				break;
		case "bedroom armoires":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Armoire";
				break;
		case "bar tables":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Bars";
				break;
		case "barstools":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Barstools";
				break;
		case "wall sculptures":
				$found = "Bed Bath & Home|Home Decor|Wall Decor |Art";
				break;
		case "game tables":
				$found = "Bed Bath & Home|Furniture|Accent Furniture|Accent Tables";
				break;
		case "vanity benches":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Vanity Tables";
				break;
		case "vanities":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Vanity Tables";
				break;
		case "wine cabinets":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Carts & Islands";
				break;
		case "wall mounted wine racks":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Baker Racks";
				break;
		case "adjustable home desk chairs":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Office Chairs";
				break;
		case "living room chairs":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Chairs & Recliners";
				break;
		case "chandeliers":
				$found = "Bed Bath & Home|Home Decor|Lighting|Ceiling Fixtures";
				break;
		case "floor lamps":
				$found = "Bed Bath & Home|Home Decor|Lighting|Floor Lamps";
				break;
		case "mattresses":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Beds";
				break;
		case "nursery rocking chairs":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Chairs & Recliners";
				break;
		case "nesting tables":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Tables";
				break;
		case "coat stands":
				$found = "Bed Bath & Home|Furniture|Accent Furniture|Coat Racks & Hall Trees";
				break;
		case "magazine holders":
				$found = "Bed Bath & Home|Furniture|Storage Furniture|Shelves & Cabinets";
				break;
		case "wall mounted mirrors":
				$found = "Bed Bath & Home|Home Decor|Wall Decor |Wall Mirrors";
				break;
		case "close to ceiling light fixtures":
				$found = "Bed Bath & Home|Home Decor|Lighting|Ceiling Fixtures";
				break;
		case "wall sconces":
				$found = "Bed Bath & Home|Home Decor|Lighting|Sconces";
				break;
		case "table lamps":
				$found = "Bed Bath & Home|Home Decor|Lighting|Table Lamps";
				break;
		case "patio sofas":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Table & Chair Sets";
				break;
		case "patio dining sets":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Table & Chair Sets";
				break;
		case "camping chairs":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Table & Chair Sets";
				break;
		case "computer desks":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Computer Furniture";
				break;
		case "dining chairs":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Chairs";
				break;
		case "home bar and bar stool sets":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Barstools";
				break;
		case "home office furniture":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Office Chairs";
				break;
		case "swivel home desk chairs":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Office Chairs";
				break;
		case "home office desk chairs":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Office Chairs";
				break;
		case "kitchen and dining room sets":
				$found = "Bed Bath & Home|Furniture|Dining Room Furniture|Dining Table & Chair Sets";
				break;
		case "portable tables":
				$found = "Bed Bath & Home|Furniture|Accent Furniture|Accent Tables";
				break;
		case "home office desks":
				$found = "Bed Bath & Home|Furniture|Office Furniture|Desks";
				break;
		case "free standing wine racks":
				$found = "Bed Bath & Home|Furniture|Kitchen Furniture|Baker Racks";
				break;
		case "bedroom furniture sets":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Beds";
				break;
		case "living room furniture sets":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Living Room Sets";
				break;
		case "standard sofas":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Sofas & Loveseats";
				break;
		case "ottomans":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Ottomans";
				break;
		case "nursery gliding ottomans":
				$found = "Bed Bath & Home|Furniture|Living Room Furniture|Ottomans";
				break;
		case "bed frames":
				$found = "Bed Bath & Home|Furniture|Bedroom Furniture|Beds";
				break;
	}

	return $found;
}
/* ########## LOG RELATED - START ########## */
// Log links to file
function log_link_file($url) {
	global $link_file, $debug;

	$link = substr($url, strlen(ROOT));
	$file = fopen($link_file, 'a+');
	if ($file) {
		fwrite($file, $link . PHP_EOL);
		logger("Write $url to $link_file");
	} else {
		logger("[ERROR] Could not save $url to $link_file");
	}
	fclose($file);
}

// Log to logfile
function logger($msg) {
	// Write to log
	$file = fopen(LOG_FILE, 'a+');
	if ($file) {
		$timestring = date('Y-m-d h:i:s', strtotime('now'));
		$msg = $timestring . ' - ' . $msg . PHP_EOL;
		fwrite($file, $msg);
	} else {
		fwrite($file, "[ERROR] Unable to open file!");
	}
	fclose($file);
}

// Log status
function log_status($msg) {
	global $status_file;
	logger($msg); // Log first
	$file = fopen($status_file, 'w');
	if ($file) fwrite($file, $msg . PHP_EOL);
	fclose($file);
}

// Log result
function log_result($msg) {
	global $result_file;
	logger($msg); // Log first
	$file = fopen($result_file, 'a+');
	if ($file) {
		fwrite($file, $msg . PHP_EOL);
	} else {
		fwrite($file, "[ERROR] Unable to open file!");
	}
	fclose($file);
}
/* ########## LOG RELATED - END ########## */

/* ########## TEST TOOLS - START ########## */
$startTime = $endTime = $duration = 0;
function stop_watch_start() {
	global $startTime, $debug;
	$startTime = microtime(true);
	if ($debug) logger("##### Start time: $startTime #####");
}

function stop_watch_stop() {
	global $startTime, $endTime, $duration, $debug;
	$endTime = microtime(true);
	$duration = $endTime - $startTime;
	$duration = round($duration, 2);
	if ($debug) { 
		logger("##### End time: $endTime #####");
		logger("##### Time executed: $duration seconds #####");
	}
}
/* ########## TEST TOOLS - END ########## */

?>

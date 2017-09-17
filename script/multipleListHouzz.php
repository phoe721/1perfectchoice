<?
/* Initialization */
require_once('functions.php');

/* Request Put Into Queue */
if (isset($_POST['url']) && isset($_POST['uid'])) {
	global $uid, $url;
	$url = urlencode($_POST['url']);
	$uid = $_POST['uid'];
	prepare($uid);
	$command = "/usr/bin/php " . __FILE__ . " $uid $url";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output
	$result['status'] = "Task put into queue for processing...";
	echo json_encode($result);
}

/* Start of Program */
if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$url = urldecode($argv[2]);
	prepare($uid);

	// Grab links from page
	log_status("Getting links from $url");
	$link_page = file_get_html($url);
	if (!empty($link_page)) {
		$links = array();
		if ($link_page->find('a.product-title')) {
			foreach($link_page->find('a.product-title') as $i=>$link) {
				$links[$i] = $link->href;
			}
		} else {
			log_status("Found no links on the page!");
		}
		$link_page->clear();
	
		// Process each link
		for ($i = 0; $i < count($links); $i++) {
			log_status("Processing page: " .  $links[$i]);
			$page = file_get_html($links[$i]);
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

				$output = "$sku\t$title\t$img_url\t$description\t$width\t$depth\t$height\t$weight\t$material\t$category";
				log_result($output);
			}
			$page->clear();
		}
	} else {
		log_status("Page is empty!");
	}

	log_link_file($result_file);
	log_status("Done");
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

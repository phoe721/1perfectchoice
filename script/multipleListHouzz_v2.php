<?
/* Initialization */
require_once('functions.php');

/* Request Put Into Queue */
if (isset($_POST['url']) && isset($_POST['start']) && isset($_POST['step']) && isset($_POST['max']) && isset($_POST['uid'])) {
	global $uid;
	$url = urlencode($_POST['url']);
	$start = $_POST['start'];
	$step = $_POST['step'];
	$max = $_POST['max'];
	$uid = $_POST['uid'];
	prepare($uid);

	// Put command into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $url $start $step $max";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output
	$result['status'] = "Task put into queue for processing...";
	echo json_encode($result);
}

/* Start of Program */
if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4]) && isset($argv[5])) {
	global $data_file, $result_file;
	$uid = $argv[1];
	$url = urldecode($argv[2]);
	$start = $argv[3];
	$step = $argv[4];
	$max = $argv[5];
	prepare($uid);

	// Grab links from page
	for ($i = $start; $i < $max; $i+=$step) {
		$page_url = $url . $i;
		log_status("Getting links from $page_url");
		$page = file_get_contents($page_url);
		preg_match_all("/https:\/\/www.houzz.com\/photos\/[0-9]{8}\/[A-Za-z0-9-]*/", $page, $matches);
		$links = implode("\n", $matches[0]);
		$file = fopen($data_file, "a+");
		if ($file) {
			fwrite($file, $links);
		} else {
			log_status("Failed to open $data_file");
		}
	}

	// Remove duplicate lines
	$lines = file($data_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$lines = array_unique($lines);
	file_put_contents($data_file, implode(PHP_EOL, $lines));

	// Get data from links
	$file1 = fopen($data_file, "r");
	$file2 = fopen($result_file, "a+");
	if ($file1 && $file2) {
		while (($line = fgets($file1)) != false) {
			log_status("Processing page: " .  $line);
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
	} else {
		log_status("Failed to open $data_file and $result_file!");
	}

	fclose($file1);
	fclose($file2);

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

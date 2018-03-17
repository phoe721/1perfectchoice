<?
/* Initialization */
require_once('functions.php');
require_once('singleList.php');

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
	$page = file_get_html($url);
	$links = array();
	if (isset($page)) {
		if ($page->find('a.product-item-link')) {
			foreach($page->find('a.product-item-link') as $i=>$link) {
				if (!empty($link->href)) {
					log_status("Found link: " . trim($link->href));
					$links[$i] = trim($link->href);
				}
			}
		}
		$page->clear();
	}

	// Process each link
	for ($i = 0; $i < count($links); $i++) {
		log_status("Processing page: " .  $links[$i]);
		$page = file_get_html($links[$i]);
		if (isset($page)) {
			process_page();
			log_status("Finish Processing page: " . $links[$i]);
		}
		$page->clear();
	}

	log_link_file($result_file);
	log_status("Done");
}
?>

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
	$page = file_get_html($url);
	$links = array();
	if ($page->find('div.product-item-details a')) {
		foreach($page->find('div.product-item-details a') as $i=>$link) {
			log_status("Found link: " . trim($link->href));
			$links[$i] = trim($link->href);
		}
	}
	$page->clear();
	
	// Process each link
	/*
	for ($i = 0; $i < count($links); $i++) {
		log_status("Processing page: " .  $links[$i]);
		$page = file_get_html($links[$i]);
		if (isset($page)) {
			get_info();
			log_status("Finish Processing page: " .  $links[$i]);
		}
		$page->clear();
	}
	*/
	
	log_status("Done");
}
?>

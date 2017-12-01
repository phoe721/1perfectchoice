<?
/* Initialization */
require_once('functions.php');

/* Start of Program */
$uid = $url = "";
if (isset($_POST['url']) && isset($_POST['uid'])) {
	global $uid, $url;
	$url = $_POST['url'];
	$uid = $_POST['uid'];
	prepare($uid);

	// Process each link
	log_status("Processing page: " . $url);
	$page = file_get_html($url);
	if (isset($page)) {
		set_page($page);
		get_info();
		log_status("Finish Processing page: " . $url);
	}

	log_status("Done");
}
?>

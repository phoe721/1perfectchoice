<?
/* ########## NOTES ##########
 * Purpose: 
 * Home page allowing page navigation
 * ########################### */

// Initialization
require_once('functions.php');
$pages = array();

// Get page directory
if (isset($_POST['getPageDirectory'])) {
	lookup_page_directory();
	echo json_encode($pages);
} else if (isset($_POST['getPage'])) {
	$page = $_POST['getPage'];
	get_page_content($page);
	echo json_encode($pages);
}

// Lookup page directory
function lookup_page_directory() {
	global $db, $pages;
	$result = $db->query("SELECT name FROM page_directory");
	if ($result) {
		if (mysqli_num_rows($result) == 0) {
			logger("Page directory table is empty!");
		} else {
			$i = 0;
			while($row = mysqli_fetch_array($result)) {
				$pages[$i]['name'] = $row['name'];
				$i++;
			}

			return $pages;
		}
	} else {
		logger("Failed to get page directory!");
	}

	return null;
}

// Get page content 
function get_page_content($page) {
	global $db, $pages;
	$result = $db->query("SELECT content FROM page_directory WHERE name = '" . $page . "'");
	if ($result) {
		if (mysqli_num_rows($result) == 0) {
			logger("Cannot find this page!");
		} else {
			$i = 0;
			$row = mysqli_fetch_array($result);
			$pages[0]['content'] = $row['content'];

			return $pages;
		}
	} else {
		logger("Failed to get page!");
	}

	return null;
}
?>

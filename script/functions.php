<?
/* Connect to Database */
require_once('init.php');
require_once('database.php');
require_once('simple_html_dom.php');

$page = $uid = $data_file = $link_file = $result_file = $status_file = $img_dir = $img_zip = '';
$product = $result = $match = array();

$db	= new database;
$db->connect("localhost", "root", "c7w2l181", "1perfectchoice");
mysqli_set_charset($db->getConnection(), "utf8");

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

// Create queue to run later
function create_queue($uid, $command) {
	global $db;
	$result = $db->query("INSERT INTO queues (command, status, insert_time, update_time) VALUES ('$command', '0', NOW(), NOW())");
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

function download($url, $path) {
	global $debug;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
	curl_setopt($ch, CURLOPT_NOPROGRESS, false); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
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
	global $db, $debug;
	$timestring = date('Y-m-d H:i:s', strtotime('now'));
	$result = $db->query("INSERT INTO 1perfectchoice_log (lid, message, datetime) VALUES ('', '$msg', '$timestring')");
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

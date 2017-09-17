<?
/* Initialization */
require_once('functions.php');

// Put request into queue
$result = array();
if (isset($_FILES["file1"]) && isset($_POST['uid'])) { 
	$uid = $_POST['uid'];
	prepare($uid);	// prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$link_file = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $link_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $link_file";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) { 
	global $img_dir;
	$uid = $argv[1];
	$link_file = $argv[2];
	prepare($uid);

	log_status("Looking up links...");
	$file1 = fopen($link_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$line = fgets($file1);
			$img_path1 = $img_path2 = "";
			if (!empty($line)) {
				list($sku, $url1, $url2) = explode("\t", $line);
				$url2 = trim($url2);
				$img_path1 = grab_amazon_img($sku, $url1);
				if (empty($img_path1)) { // Small Image
					log_result("Failed to get Amazon image for $sku, too small!");
				} else {
					$check = @fopen($url2, "r");
					if ($check) {
						$img_path2 = grab_img2($sku, $url2);
						if (!empty($img_path2)) {
							$result = compare_images($img_path1, $img_path2);
							if ($result) {
								log_result("For $sku, two images are the same!");
							} else {
								log_result("For $sku, two images are the different!");
							}
						} else {
							log_result("Failed to get product image 2 for $sku!");
						}
					} else {
						log_result("Failed to get product image 2 for $sku, invalid link!");
					}
				}
			}
		}
	} else {
		logger("Failed to open $link_file and $result_file");
	}
	fclose($file1);

	log_status("Done!");
}
?>

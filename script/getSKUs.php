<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if(isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$input_file = AMAZON_UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $input_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $input_file";
	$qid = create_queue($uid, $command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
}

if (isset($argv[1]) && isset($argv[2])) {
	$uid = $argv[1];
	$input_file = $argv[2];
	prepare($uid);

	log_status("Separating SKUs...");
	$file1 = fopen($input_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$skus = fgets($file1);
			$skus = trim($skus);
			$sku = separate_sku($skus);
			$output = $skus . "\t" . implode("\t", $sku);
			log_result($output);
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
}

function separate_sku($skus) {
	$sku = preg_split("/-/", $skus);
	$sku1Len = strlen($sku[0]);
	for ($i = 1; $i < count($sku); $i++) {
		$curLen = strlen($sku[$i]);
		if ($sku1Len > $curLen) {
			$diff = $sku1Len - $curLen;
			$sku[$i] = substr($sku[0], 0, $diff) . $sku[$i];
		}
	}

	return $sku;
}
?>

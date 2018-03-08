<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if(isset($_FILES["file1"]) && isset($_POST['uid'])) {
	$uid = $_POST['uid'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$input_file = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
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

	log_status("Looking up keywords...");
	$file1 = fopen($input_file, "r");
	if ($file1) {
		while (($line = fgets($file1)) !== false) {
			$line = trim($line);
			$line = strtolower($line);
			$line = filter_bad_keyword($line);	
			$line = filter($line);
			$keywords = preg_replace('/\s/', "\t", $line);
			log_result($keywords);
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
}

function filter_bad_keyword($str) {
	$badKeywords = array('\d+',',','"','\dpcs','\+','\&','the','of','\d','in','with');

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
	$str = preg_replace('/\(\d+\)/', '', $str);		// Remove (numbers) 
	$str = preg_replace('/w\//', 'with ', $str);	// Replace "w/" with "with "
	$str = preg_replace('/Drw/', 'Drawer ', $str);	// Replace "Drw" with "Drawer "
	$str = preg_replace('/\$/', '', $str);			// Remove $
	$str = preg_replace('/\*/', '', $str);			// Remove *
	$str = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $str);
	$str = preg_replace('/[A-Z0-9]{2,5}-[A-Z0-9]{3,15}{-}*[A-Z0-9]*/','', $str);
	$str = preg_replace('/\s\s+/', ' ', $str);		// Remove extra spaces
	$str = strip_tags($str);	// Strip HTML tags
	$str = trim($str);			// Trim spaces

	return $str;
}
?>

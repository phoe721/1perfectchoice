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
			$line = filter($line);
			$line = filter_bad_keyword($line);	
			$keywords = preg_replace('/\s/', "\t", $line);
			log_result($keywords);
		}
	} else {
		logger("Failed to open $input_file");
	}
	fclose($file1);

	log_link_file($result_file);
	log_status("Done!");
} else if (isset($argv[1])) {
	$input = $argv[1];
	$input = trim($input);
	$input = strtolower($input);
	$input = filter($input);
	$input = filter_bad_keyword($input);	
	$keywords = preg_replace('/\s/', ' ', $input);
	echo "Keywords: " . $keywords . PHP_EOL;	
}

function filter_bad_keyword($str) {
	$badKeywords = array('the','of','with','set','by','only','in','and','up','or','[A-Za-z]{1}');

	foreach($badKeywords as $word) {
		$str = preg_replace('/ ' . $word . '$/', '', $str);
		$str = preg_replace('/^' . $word . ' /', '', $str);
		$str = preg_replace('/ ' . $word . ' /', '', $str);
	}

	// Check if it's valid English word
	$pspell_link = pspell_new("en");
	$newStr = '';
	$pieces = explode(' ', $str);
	$pieces = array_unique($pieces);
	foreach($pieces as $word) {
		if (pspell_check($pspell_link, $word)) {
			$newStr .= $word . ' ';
		}
	}

	$newStr = preg_replace('/\s\s+/', ' ', $newStr);	// Remove extra spaces
	$newStr = trim($newStr);
	return $newStr;
}

function filter($str) {
	$str = preg_replace('/' . PHP_EOL . '/', ' ', $str);
	$str = preg_replace('/(w\/|-|\|)/', '', $str);	// Remove w/,-,| 
	$str = preg_replace('/(\+|\/)/', ' ', $str);	// Replace plus sign to space
	$str = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $str);
	$str = preg_replace('/\(\d+\)/', '', $str);	// Remove (numbers) 
	$str = preg_replace('/\d(pcs|pc)/', '', $str);	// Remove Npcs
	$str = preg_replace('/\d+/', '', $str);	// Remove numbers 
	$str = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $str);
	$str = preg_replace('/[A-Z0-9]{2,5}-[A-Z0-9]{3,15}{-}*[A-Z0-9]*/','', $str);
	$str = preg_replace('/\s\s+/', ' ', $str);	// Remove extra spaces
	$str = trim($str);	// Trim spaces

	return $str;
}
?>

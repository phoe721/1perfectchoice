<?
/* Initialization */
require_once('functions.php');
require_once('class/queues.php');
$q = new queues;

// Put Request Into Queue
if (isset($_FILES["file1"]) && isset($_POST['uid']) && isset($_POST["server"]) && isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["directory"])) {
	$uid = $_POST['uid'];
	$server = $_POST['server'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$directory = $_POST['directory'];
	prepare($uid);	// Prepare directory

	// Move upload file1
	$tmp_file1 = $_FILES["file1"]["tmp_name"];
	$link_file = UPLOAD . $uid . '/' . basename($_FILES["file1"]["name"]);
	move_uploaded_file($tmp_file1, $link_file) ;

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $server $user $pass $directory $link_file";
	$qid = $q->create_queue($command);

	// Log status
	log_status("Queue created, your queue number is $qid!");

	// Output status
	$result['status'] = "Files uploaded!";
	echo json_encode($result);
} else if (isset($_POST["server"]) && isset($_POST["user"]) && isset($_POST["pass"])) {
	$server = $_POST["server"];
	$user = $_POST["user"];
	$pass = $_POST["pass"];
	$conn = ftp_connect($server) or die("Couldn't connect to $server");

	// Login to FTP server
	if (@ftp_login($conn, $user, $pass)) {
		ftp_pasv($conn, true); // Turn Passive Mode On
		$result['status'] = "Successfully login to $server!";
		$result['files'] = ftp_nlist($conn, "/images");
		$file_array = array();
		array_push($file_array, ".");
		array_push($file_array, "..");
		foreach ($result['files'] as $file) {
			if (preg_match('/(\.)(gif|jpg|png|txt|swf|db|ico)$/i', $file)) {
				array_push($file_array, $file);	
			}
		}
		$result['files'] = array_values(array_diff($result['files'], $file_array));
	} else {
		$result['status'] = "Failed to login to $server!";
	}

	echo json_encode($result);
	ftp_close($conn);
}

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4]) && isset($argv[5]) && isset($argv[6])) {
	global $img_dir;
	$uid = $argv[1];
	$server = $argv[2];
	$user = $argv[3];
	$password = $argv[4];
	$directory = $argv[5];
	$link_file = $argv[6];
	prepare($uid);

	log_status("Looking up links...");
	$total = count(file($link_file));
	$pass = $fail = $count = 0;
	$file1 = fopen($link_file, "r");
	if ($file1) {
		while (!feof($file1)){
			$line = fgets($file1);
			if (!empty($line)) {
				list($sku, $url) = explode("\t", $line);
				$url = trim($url);
				$check = @fopen($url, "r");
				if ($check) {
					$img_path = grab_img($sku, $url);
					if (!empty($img_path)) {
						//log_result("Received product image for: $sku!");
						$pass++;
					} else {
						log_result("Failed to get product image for $sku!");
						$fail++;
					}
					$count++;
					log_status("Pass: $pass Fail: $fail Progress: $count / $total");
				} else {
					log_result("Failed to get product image for $sku, invalid link!");
				}

			}
		}
	} else {
		logger("Failed to open $link_file and $result_file");
	}
	fclose($file1);

	// Upload image files to server
	upload_img_dir($server, $user, $password, $directory, $img_dir);	

	log_status("Done!");
}
?>

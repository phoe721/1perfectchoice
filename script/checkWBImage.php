<?
/* Initialization */
require_once('functions.php');

// Put Request Into Queue
if (isset($_POST["uid"]) && isset($_POST["server"]) && isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["selected"])) {
	$uid = $_POST["uid"];
	$server = $_POST["server"];
	$user = $_POST["user"];
	$pass = $_POST["pass"];
	$selected = $_POST["selected"];
	prepare($uid);

	// Put task into queue
	$command = "/usr/bin/php " . __FILE__ . " $uid $server $user $pass $selected";
	$qid = create_queue($uid, $command);

	// Log status
	$result['status'] = "Queue created, your queue number is $qid!";

	// Output status
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
		$result['files'] = ftp_nlist($conn, "/images"); // Change to image directory
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


if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]) && isset($argv[4]) && isset($argv[5])) {
	$uid = $argv[1];
	$server = $argv[2];
	$user = $argv[3];
	$pass = $argv[4];
	$selected = $argv[5];
	$path = "/images/" . $selected;
	prepare($uid);

	// Login to FTP server
	$file_array = array();
	$conn = ftp_connect($server) or die("Couldn't connect to $server");
	if (@ftp_login($conn, $user, $pass)) {
		ftp_pasv($conn, true); // Turn Passive Mode On
		$result['status'] = "Successfully login to $server!";
		$result['files'] = ftp_nlist($conn, $path); // Change to image directory
		foreach ($result['files'] as $file) {
			if (preg_match('/(\.)(jpg)$/i', $file)) {
				$remote_file = $path . '/' . $file;
				$local_file = $img_dir . $file;
				if (ftp_get($conn, $local_file, $remote_file, FTP_BINARY)) {
					log_status("Successfully downloaded $file!");
					$image = new imagick();
					$image->readImage($local_file);
					$count = 0;
					for ($x = 0; $x < 5; $x++) {
						for ($y = 0; $y < 5; $y++) {
							$pixel = $image->getImagePixelColor($x, $y);
							$color = $pixel->getColorAsString();
							if ($color == "rgb(255,255,255)") $count++;
							//echo "($x, $y) Color: $color\n";
						}
					}
					if ($count == 25) {
						$output = "$$remote_file\tWhite Background";
						log_result($output);
					}
				} else {
					log_status("Failed to download $file!");
				}
			}
		}
	} else {
		log_status("Failed to login to $server!");
	}

	ftp_close($conn);
	log_link_file($result_file);
	log_status("Done!");
}
?>

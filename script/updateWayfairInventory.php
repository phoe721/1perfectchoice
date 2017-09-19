<?
/* Initialization */
require_once('functions.php');

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
	$inventory_file = UPLOAD . $uid . '/inventory.csv';
	move_uploaded_file($tmp_file1, $inventory_file) ;
	logger("Inventory file path: $inventory_file.");

	// Record login to DB 
	$db_result = $db->query("INSERT INTO wayfair (server, user, pass, directory, path) VALUES ('$server', '$user', '$pass', '$directory', '$inventory_file')");
	if ($db_result) {
		logger("Login recorded to DB!");
	} else {
		logger("Failed to insert to DB!");
	}

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
		$result['files'] = ftp_nlist($conn, "/");
	} else {
		$result['status'] = "Failed to login to $server!";
	}

	echo json_encode($result);
	ftp_close($conn);
} else {
	$result = $db->query("SELECT * FROM wayfair ORDER BY id DESC LIMIT 1");
	if (mysqli_num_rows($result) == 0) {
		logger("No records in DB");
	} else {
		$row = mysqli_fetch_array($result);
		$server = $row['server'];
		$user = $row['user'];
		$pass = $row['pass'];
		$remote_dir = $row['directory'];
		$file = $row['path'];
		$remote_file = $remote_dir . '/inventory.csv'; 
		$conn = ftp_connect($server) or die("Couldn't connect to $server");

		// Login to FTP server
		if (@ftp_login($conn, $user, $pass)) {
			ftp_pasv($conn, true); // Turn Passive Mode On
			logger("Successfully login to $server!");
			if (ftp_put($conn, $remote_file, $file, FTP_BINARY)) {
				logger("Upload $file successfully!");
			} else {
				logger("Failed to upload $file!");
			}
		} else {
			logger("Failed to login to $server!");
		}

		ftp_close($conn);
	}
}
?>

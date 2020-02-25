<?
require_once("class/upload.php");
require_once("class/ftp_client.php");
$upload = new upload();
$ftp_client = new ftp_client();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"]) && isset($_POST["uid"])) { 
	$upload->set_UID($_POST["uid"]);
	$upload->set_file($_FILES["file"]);
	$output = $upload->get_error();
	if ($output == "File uploaded!") {
		$fileName = basename($upload->get_filename());
		list($code, $file) = explode("-", $fileName, 2);
		if($ftp_client->connect(FTP_SERVER) && $ftp_client->login(FTP_USER, FTP_PASS)) {
			$ftp_client->set_passive();
			$ftp_client->change_dir("/images/" . $code);
			$ftp_client->put($file, $upload->get_targetFile());
			$output = "$file uploaded";
		} else {
			$output = FTP_SERVER . FTP_USER . FTP_PASS;
			//$output = "Failed to login to FTP server!";
		}
	}

	echo json_encode($output);
}

// Disconnect from server
$ftp_client->disconnect();
?>

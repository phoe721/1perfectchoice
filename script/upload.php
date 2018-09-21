<?
/* Initialization */
require_once('init.php');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['task']) && isset($_POST['uid'])) { 
	$name = $_FILES['file']['name'];
	$tmpName = $_FILES['file']['tmp_name'];
	$error = $_FILES['file']['error'];
	$size = $_FILES['file']['size'];
	$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	$task = $_POST['task'];
	$uid = $_POST['uid'];
	$targetDir = UPLOAD . $uid . '/';
	if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

	switch($error) {
		case UPLOAD_ERR_OK:
			$sourceFile = $tmpName;
			$targetFile = $targetDir . basename($name);
			if (file_exists($targetFile)) {
				$response = "File already exists!";
			} else {
				if ($size > MAX_UPLOAD_SIZE) {
					$response = "File size too large!";
				} else {
					if (move_uploaded_file($sourceFile, $targetFile)) { 
						$response = "File uploaded!";
					} else {
						$response = "File upload fail!";
					}
				}
			}
			break;
		case UPLOAD_ERR_INI_SIZE: 
			$response = "File size exceeds maximum file size specificed in php.ini!";
			break;
		case UPLOAD_ERR_FORM_SIZE: 
			$response = "File size exceeds maximum file size specificed in HTML form!";
			break;
		case UPLOAD_ERR_PARTIAL:
			$response = "File only partially uploaded!";
			break;
		case UPLOAD_ERR_NO_FILE:
			$response = "No file was uploaded!";
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$response = "Missing temporary folder!";
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$response = "Failed to write file to disk!";
			break;
		case UPLOAD_ERR_EXTENSION:
			$response = "PHP extension stopped the file upload!";
			break;
		default:
			$response = 'Unknown error';
	}
	echo $response;
}
echo "<br><br>";
echo "<a href='../upload.htm'>Go back to upload</a>";

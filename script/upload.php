<?
/* Initialization */
require_once('init.php');

if(isset($_FILES["file"])) { 
	$error = $_FILES["file"]["error"];
	switch($error) {
		case 0:
			$sourceFile = $_FILES["file"]["tmp_name"];
			$targetFile = UPLOAD . basename($_FILES["file"]["name"]);
			if (file_exists($targetFile)) {
				echo "File already exists!";
			} else {
				if ($_FILES["file"]["size"] > MAX_UPLOAD_SIZE) {
					echo "File size too large!";
				} else {
					if (move_uploaded_file($sourceFile, $targetFile)) { 
						echo "File uploaded!";
					} else {
						echo "File upload fail!";
					}
				}
			}
			break;
		case 1: 
			echo "File size exceeds maximum file size specificed in php.ini!";
			break;
		case 2: 
			echo "File size exceeds maximum file size specificed in HTML form!";
			break;
		case 3:
			echo "File only partially uploaded!";
			break;
		case 4:
			echo "No file was uploaded!";
			break;
		case 6:
			echo "Missing temporary folder!";
			break;
		case 7:
			echo "Failed to write file to disk!";
			break;
		case 8:
			echo "PHP extension stopped the file upload!";
			break;
	}
}
echo "<br><br>";
echo "<a href='../upload.html'>Go back to upload</a>";

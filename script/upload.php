<?
/* Initialization */
require_once('init.php');

if(isset($_FILES["file"])) {
	$sourceFile = $_FILES["file"]["tmp_name"];
	$targetFile = UPLOAD . basename($_FILES["file"]["name"]);
	move_uploaded_file($sourceFile, $targetFile) ;
	echo "File uploaded!";
} else {
	echo "File upload fail!";
}
echo "<br><br>";
echo "<a href='../upload.html'>Go back to upload</a>";

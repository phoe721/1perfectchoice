<?
/* Initialization */
require_once('class/upload.php');
$upload = new upload();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['task']) && isset($_POST['uid'])) { 
	$upload->set_UID($_POST['uid']);
	$upload->set_task($_POST['task']);
	$upload->set_file($_FILES['file']);
	echo $upload->get_error();
}
echo "<br><br>";
echo "<a href='../upload.htm'>Go back to upload</a>";

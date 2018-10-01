<?
/* Initialization */
require_once('class/upload.php');
require_once('class/queues.php');
$upload = new upload();
$queue = new queues();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['task']) && isset($_POST['uid'])) { 
	$upload->set_UID($_POST['uid']);
	$upload->set_file($_FILES['file']);
	if ($upload->get_error() == "File uploaded!") {
		$task = $_POST['task'];
		switch($task) {
			case "check_costs":
				$script = SCRIPT_ROOT . "checkCosts.php";
				$command = "/usr/bin/php $script " . $upload->get_UID() . " " . $upload->get_targetFile() . " " . $upload->get_outputFile();
				$qid = $queue->create_queue($command);
				break;
		}
	} else {
		echo $upload->get_error();
	}
}
echo "<br><br>";
echo "<a href='../upload.htm'>Go back to upload</a>";

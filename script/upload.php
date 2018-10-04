<?
/* Initialization */
require_once("class/upload.php");
require_once("class/queues.php");
$upload = new upload();
$queue = new queues();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"]) && isset($_POST["task"]) && isset($_POST["uid"])) { 
	$upload->set_UID($_POST["uid"]);
	$upload->set_file($_FILES["file"]);
	$script = "";
	$output = $upload->get_error();
	if ($output == "File uploaded!") {
		$task = $_POST["task"];
		$script = SCRIPT_ROOT . $task . ".php";
		$command = "/usr/bin/php $script " . $upload->get_targetFile() . " " . $upload->get_outputFile() . " " . $upload->get_statusFile();
		$qid = $queue->create_queue($command);
		$output .= " Queue created, your queue number is $qid!";
	}

	echo json_encode($output);
}

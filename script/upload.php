<?
/* Initialization */
require_once('class/upload.php');
require_once('class/queues.php');
$upload = new upload();
$queue = new queues();
$script = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && isset($_POST['task']) && isset($_POST['uid'])) { 
	$upload->set_UID($_POST['uid']);
	$upload->set_file($_FILES['file']);
	if ($upload->get_error() == "File uploaded!") {
		$task = $_POST['task'];
		switch($task) {
			case "check_costs":
				$script = SCRIPT_ROOT . "checkCosts.php";
				break;
			case "check_discontinued":
				$script = SCRIPT_ROOT . "checkDiscontinued.php";
				break;
			case "check_inventory":
				$script = SCRIPT_ROOT . "checkInventory.php";
				break;
			case "get_category":
				$script = SCRIPT_ROOT . "getCategory.php";
				break;
		}

		$command = "/usr/bin/php $script " . $upload->get_targetFile();
		$qid = $queue->create_queue($command);
		echo "Queue created, your queue number is $qid!";
	} else {
		echo $upload->get_error();
	}
}
echo "<br><br>";
echo "<a href='../upload.htm'>Go back to upload</a>";

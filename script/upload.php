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
				$command = "/usr/bin/php $script " . $upload->get_UID() . " " . $upload->get_targetFile();
				$qid = $queue->create_queue($upload->getUID(), $command);
				echo "Queue created, your queue number is $qid!"
				break;
			case "check_discontinued":
				$script = SCRIPT_ROOT . "checkDiscontinued.php";
				$command = "/usr/bin/php $script " . $upload->get_UID() . " " . $upload->get_targetFile();
				$qid = $queue->create_queue($upload->getUID(), $command);
				echo "Queue created, your queue number is $qid!"
				break;
			case "check_inventory":
				$script = SCRIPT_ROOT . "checkInventory.php";
				$command = "/usr/bin/php $script " . $upload->get_UID() . " " . $upload->get_targetFile();
				$qid = $queue->create_queue($upload->getUID(), $command);
				echo "Queue created, your queue number is $qid!"
				break;
			case "get_category":
				$script = SCRIPT_ROOT . "getCategory.php";
				$command = "/usr/bin/php $script " . $upload->get_UID() . " " . $upload->get_targetFile();
				$qid = $queue->create_queue($upload->getUID(), $command);
				echo "Queue created, your queue number is $qid!"
				break;
		}
	} else {
		echo $upload->get_error();
	}
}
echo "<br><br>";
echo "<a href='../upload.htm'>Go back to upload</a>";

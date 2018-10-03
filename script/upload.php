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
		switch($task) {
			case "insert_costs":
				$script = SCRIPT_ROOT . "insertCosts.php";
				break;
			case "insert_dimensions":
				$script = SCRIPT_ROOT . "insertDimensions.php";
				break;
			case "check_costs":
				$script = SCRIPT_ROOT . "checkCosts.php";
				break;
			case "check_shipping":
				$script = SCRIPT_ROOT . "checkShipping.php";
				break;
			case "check_dimensions":
				$script = SCRIPT_ROOT . "checkDimensions.php";
				break;
			case "check_weights":
				$script = SCRIPT_ROOT . "checkWeights.php";
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

		$command = "/usr/bin/php $script " . $upload->get_targetFile() . " " . $upload->get_outputFile() . " " . $upload->get_statusFile();
		$qid = $queue->create_queue($command);
		$output .= " Queue created, your queue number is $qid!";
	}

	echo json_encode($output);
}

<?
/* Initialization */
require_once("class/upload.php");
$upload = new upload();
$result = array();

if (isset($_POST["uid"])) {
	$upload->set_UID($_POST["uid"]);
	$outputFile = $upload->get_outputFile();
	$statusFile = $upload->get_statusFile();

	// Push output to result
	if (file_exists($outputFile)) {
		$result["link"] = str_replace(ROOT, "", $outputFile);
	}

	// Output status
	if (file_exists($statusFile)) {
		$file = fopen($statusFile, "r") or die("Unable to open file!");
		if ($file) $result["status"] = trim(fgets($file));
		fclose($file);
		echo json_encode($result);
	}
}
?>

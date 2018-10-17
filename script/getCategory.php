<?
require_once('class/category.php');
require_once('class/status.php');
$cat = new category();
$status = new status();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$inputFile = $argv[1];
	$outputFile = $argv[2];
	$statusFile = $argv[3];
	$status->set_file($statusFile);
	$input = fopen($inputFile, "r");
	$output = fopen($outputFile, "a+");
	if ($input && $output) {
		while(!feof($input)) {
			$title = trim(fgets($input));
			if (!empty($title)) {
				$status->log_status("Checking $title...");
				$category = $cat->get_category($title);
				$result = "$title\t$category" . PHP_EOL;
				fwrite($output, $result);
			}
		}
	}

	$status->log_status("Done!");
	fclose($input);
	fclose($output);
}
?>

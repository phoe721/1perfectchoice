<?
require_once('class/category.php');
$cat = new category();

if (isset($argv[1]) && isset($argv[2])) {
	$inputFile = $argv[1];
	$outputDir = $argv[2];
	$outputFile = $outputDir . "/result.txt";
	$file = fopen($inputFile, "r");
	$file2 = fopen($outputFile, "a+");
	if ($file && $file2) {
		while(!feof($file)) {
			$title = trim(fgets($file));
			if (!empty($title)) {
				$output = $title . "\t" . $cat->get_category($title) . PHP_EOL;
				fwrite($file2, $output);
			}
		}
	}
	fclose($file);
	fclose($file2);
} else if (isset($argv[1])) {
	$input = $argv[1];
	echo $cat->get_category($input);
}
?>

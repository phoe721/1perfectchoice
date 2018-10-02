<?
require_once('class/category.php');
$cat = new category();

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
	$uid = $argv[1];
	$input = $argv[2];
	$output = $argv[3];
	$file = fopen($input, "r");
	$file2 = fopen($output, "a+");
	if ($file && $file2) {
		while(!feof($file)) {
			$title = trim(fgets($file));
			if (!empty($line)) {
				$output = $title . "\t" . get_category($title) . PHP_EOL;
				fwrite($file2, $output);
			}
		}
	}
	fclose($file);
	fclose($file2);
}
?>

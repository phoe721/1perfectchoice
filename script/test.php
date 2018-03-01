<?	
/* Initialization */
require_once("functions.php");
$input = UPLOAD . "input.txt";
$handle = fopen($input, "r");
if ($handle) {
	while (($line = fgets($handle)) !== false) {
		echo $line;			
	}
	fclose($handle);
} else {
	echo "Failed to open $input";
}
?>

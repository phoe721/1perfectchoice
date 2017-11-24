<?	
/* Initialization */
require_once('functions.php');

$file1 = UPLOAD . "21080Q.jpg";
$file2 = UPLOAD . "21080Q_L.jpg";

$result = compare_images($file1, $file2);
echo $result;
if ($result == 0) {
	echo "Two images are the same!";
} else {
	echo "Two images are the different!";
}

?>

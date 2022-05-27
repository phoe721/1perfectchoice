<?
require_once("class/init.php");
require_once("class/debugger.php");
$remote_dir = SERVER2_INVENTORY_FOLDER . date("Y") . "/" . date("n") . "/";
$local_dir = INVENTORY;
$output = new debugger();

$output->info("Server directory: $remote_dir");
if (is_dir($remote_dir)) {
	$dir = array_diff(scandir($remote_dir), array('..', '.'));
	foreach ($dir as $key => $file) {
		if(is_dir("$file")) {
			// Do nothing
		} else {
			$today = date("Ymd");
			if (preg_match("/.*-" . $today . "/", $file)) {
				$output->info("Current file: $file");
				if (preg_match("/DSC/", $file)) {
					$new_file = preg_replace("/" . $today . "/", "", $file);
				} else {
					$new_file = preg_replace("/-" . $today . "/", "", $file);
				}
				$output->info("New file: $new_file");
				$remote_file = $remote_dir . $file;
				$local_file = $local_dir . $new_file;
				$output->info("Going to copy $remote_file to $local_file");
				if (!copy($remote_file, $local_file)) {
					$output->notice("Failed to copy $remote_file");
				} else {
					$output->info("$remote_file copied to $local_file successfully");;
				}
			} else {
				$output->info("Current file: $file, not today's inventory file");
			}
		}
	}
} else {
	$output->error("$remote_dir is not a directory!");
}
?>

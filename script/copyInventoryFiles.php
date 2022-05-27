<?
require_once("class/init.php");
require_once("class/debugger.php");
require_once("class/zipper.php");
require_once("class/helper_functions.php");
$remote_dir = SERVER2_INVENTORY_FOLDER . date("Y") . "/" . date("n") . "/";
$zipper = new zipper();
$output = new debugger();
$output->set_console(false);

//$output->info("Server directory: $remote_dir");
if (is_dir($remote_dir)) {
	$dir = array_diff(scandir($remote_dir), array('..', '.'));
	foreach ($dir as $key => $file) {
		if(is_dir("$file")) {
			// Do Nothing
		} else {
			$date_str = date("Ymd");
			$today_str = date("md");
			if (preg_match("/.*-" . $date_str . "/", $file)) {
				//$output->info("Current file: $file");
				if (preg_match("/DSC/", $file)) {
					$new_file = preg_replace("/" . $date_str . "/", "", $file);
				} else {
					$new_file = preg_replace("/-" . $date_str . "/", "", $file);
				}
				//$output->info("New file: $new_file");
				$remote_file = $remote_dir . $file;
				$local_file = INVENTORY . $new_file;
				//$output->info("Going to copy $remote_file to $local_file");
				if (!copy($remote_file, $local_file)) {
					$output->notice("Failed to copy $remote_file");
				} else {
					$output->info("$remote_file copied to $local_file successfully");;
				}
			} else if (preg_match("/" . $today_str . "\.zip/", $file)) {
				//$output->info("Current file: $file");
				$remote_file = $remote_dir . $file;
				$extract_dir = INVENTORY . $today_str . "/";
				$zipper->unzip_files($remote_file, INVENTORY);
				$dir2 = array_diff(scandir($extract_dir), array('..', '.'));
				foreach ($dir2 as $key => $file2) {
					if (preg_match("/Amazon*|Houzz*|Overstock*|Wayfair*/", $file2)) {
						$source_path = $extract_dir . $file2;
						$target_path = INVENTORY . $file2;
						if (!copy($source_path, $target_path)) {
							$output->notice("Failed to copy $source_path");
						} else {
							$output->info("$source_path copied to $target_path successfully");;
						}
					}
				}
				rrmdir($extract_dir);
			} else {
				//$output->info("Current file: $file, not today's inventory file");
			}
		}
	}
} else {
	$output->error("$remote_dir is not a directory!");
}
?>

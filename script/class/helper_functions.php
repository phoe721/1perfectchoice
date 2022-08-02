<?php
	function replace_vendor($code, $item_no) { 
		$replace_code = $code;
		$replace_item_no = $item_no;
		if ($replace_code == "SR") {
			switch ($item_no) {
				case (preg_match('/^01/', $item_no) ? true : false):
					$replace_code = "PDEX";
					if (strlen($item_no) > 5) {
						$replace_item_no = preg_replace('/^01/', 'F', $item_no);
					} else {
						$replace_item_no = preg_replace('/^01/', '', $item_no);
					}
					break;
				case (preg_match('/^02/', $item_no) ? true : false):
					$replace_code = "AC";
					$replace_item_no = preg_replace('/^02/', '', $item_no);
					break;
				case (preg_match('/^03/', $item_no) ? true : false):
					$replace_code = "FA";
					$replace_item_no = preg_replace('/^03/', '', $item_no);
					break;
				case (preg_match('/^04/', $item_no) ? true : false):
					$replace_code = "CO";
					$replace_item_no = preg_replace('/^04/', '', $item_no);
					break;
				case (preg_match('/^05/', $item_no) ? true : false):
					$replace_code = "LHF";
					$replace_item_no = preg_replace('/^05/', '', $item_no);
					break;
				case (preg_match('/^06/', $item_no) ? true : false):
					$replace_code = "LSI";
					$replace_item_no = preg_replace('/^06/', '', $item_no);
					break;
			}
		}

		return array($replace_code, $replace_item_no);
	}

	function clean_up($sku) { 
		$sku = preg_replace('/-local.*$/i', '', $sku);
		$sku = preg_replace('/\+/', '-', $sku);
		$sku = preg_replace('/^SR/', 'SR-', $sku);
		return $sku;
	}

	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object)) {
						rrmdir($dir. DIRECTORY_SEPARATOR .$object);
					} else {
						unlink($dir. DIRECTORY_SEPARATOR .$object);
					}
				}
			}
			rmdir($dir);
		}
	}
?>

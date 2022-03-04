<?php
	function replace_vendor($code, $item_no) { 
		$replace_code = $code;
		$replace_item_no = $item_no;
		switch ($item_no) {
			case (preg_match('/^01/', $item_no) ? true : false):
				$replace_code = "PDEX";
				$replace_item_no = preg_replace('/^01/', 'F', $item_no);
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
		}
		return array($replace_code, $replace_item_no);
	}
?>

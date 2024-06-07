<?php
	use PHPUnit\Framework\TestCase;
	require '../class/ASIN.php';

	class ASINTest extends TestCase {
		public function testCheckExists() {
			$asin = new ASIN();
			$code = "AC";
			$item_no = "00118";
			$this->assertEquals(true, $asin->check_exist($code, $item_no));
		}

		public function testCheckExists2() {
			$asin = new ASIN();
			$code = "ABCD";
			$item_no = "12345";
			$this->assertEquals(false, $asin->check_exist($code, $item_no));
		}
	}
?>

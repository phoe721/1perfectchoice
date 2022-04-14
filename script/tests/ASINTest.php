<?php
	use PHPUnit\Framework\TestCase;

	class ASINTest extends TestCase {
		public function testCheckExists() {
			require '../class/ASIN.php';
			$asin = new ASIN();
			$code = "AC";
			$item_no = "07303";

			$this->assertEquals(true, $asin->check_exist($code, $item_no));
		}
	}
?>

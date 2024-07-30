<?php
use PHPUnit\Framework\TestCase;
require_once("../class/database.php");
require_once("../class/init.php");

class DatabaseTest extends TestCase {
    private $db;

    protected function setUp(): void {
        $this->db = Database::getInstance();
    }

    public function testConnection() {
        $this->assertInstanceOf(mysqli::class, $this->db->getConnection());
    }

    public function testQuerySuccess() {
        $result = $this->db->query("SELECT 1");
        $this->assertTrue($result !== false);
    }

    public function testQueryFailure() {
        $result = $this->db->query("SELECT * FROM non_existing_table");
        $this->assertFalse($result);
    }

    public function testEscapeString() {
        $unsafeString = "' OR '1'='1";
        $escapedString = $this->db->real_escape_string($unsafeString);
        $this->assertNotEquals($unsafeString, $escapedString);
    }

    public function testLastInsertId() {
        $this->db->query("CREATE TABLE IF NOT EXISTS test_table (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255))");
        $this->db->query("INSERT INTO test_table (name) VALUES ('Test')");
        $lastId = $this->db->last_insert_id();
        $this->assertIsInt($lastId);
        $this->db->query("DROP TABLE test_table");
    }

    public function testGetInfo() {
        $this->db->query("SELECT 1");
        $info = $this->db->get_info();
        $this->assertNotFalse($info);
    }

    public function testError() {
        $this->db->query("SELECT * FROM non_existing_table");
        $error = $this->db->error();
        $this->assertStringContainsString("1146", $error); // 1146 is a MySQL error code for table doesn't exist
    }
}
?>

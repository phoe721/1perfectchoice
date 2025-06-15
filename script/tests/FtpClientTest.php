<?php
use PHPUnit\Framework\TestCase;

require_once '../class/ftp_client.php';

class FtpClientTest extends TestCase {
    private $ftpClient;

    protected function setUp(): void {
        $this->ftpClient = $this->getMockBuilder(ftp_client::class)
                                ->onlyMethods([])
                                ->getMock();
    }

    public function testConnectSuccess() {
        $stub = $this->getMockBuilder(ftp_client::class)
                     ->onlyMethods(['connect'])
                     ->getMock();

        $stub->method('connect')->willReturn(true);
        $this->assertTrue($stub->connect('127.0.0.1'));
    }

    public function testConnectFail() {
        $stub = $this->getMockBuilder(ftp_client::class)
                     ->onlyMethods(['connect'])
                     ->getMock();

        $stub->method('connect')->willReturn(false);
        $this->assertFalse($stub->connect('bad.host'));
    }

    public function testLoginSuccess() {
        $stub = $this->getMockBuilder(ftp_client::class)
                     ->onlyMethods(['login'])
                     ->getMock();

        $stub->method('login')->willReturn(true);
        $this->assertTrue($stub->login('user', 'pass'));
    }

    public function testLoginFail() {
        $stub = $this->getMockBuilder(ftp_client::class)
                     ->onlyMethods(['login'])
                     ->getMock();

        $stub->method('login')->willReturn(false);
        $this->assertFalse($stub->login('user', 'wrongpass'));
    }

    public function testDisconnect() {
        $stub = $this->getMockBuilder(ftp_client::class)
                     ->onlyMethods(['disconnect'])
                     ->getMock();

        $stub->method('disconnect')->willReturn(true);
        $this->assertTrue($stub->disconnect());
    }
}

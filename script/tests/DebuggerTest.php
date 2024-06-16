<?php

use PHPUnit\Framework\TestCase;

require_once '../class/Debugger.php'; // Update this path to the actual location of debugger.php

class DebuggerTest extends TestCase
{
    private Debugger $debugger;
    private string $logFile = '/tmp/test.log';

    protected function setUp(): void
    {
        $this->debugger = new Debugger();
        $this->debugger->set_log_level(3);
        $this->debugger->set_console(false);
        
        // Update the log file path for testing
        $reflection = new ReflectionClass($this->debugger);
        $property = $reflection->getProperty('log_file');
        $property->setAccessible(true);
        $property->setValue($this->debugger, $this->logFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testInfoLog()
    {
        $this->debugger->info('This is an info message');
        $this->assertLogContains('[Info]');
    }

    public function testNoticeLog()
    {
        $this->debugger->notice('This is a notice message');
        $this->assertLogContains('[Notice]');
    }

    public function testErrorLog()
    {
        $this->debugger->error('This is an error message');
        $this->assertLogContains('[Error]');
    }

    public function testWarningLog()
    {
        $this->debugger->warning('This is a warning message');
        $this->assertLogContains('[Warning]');
    }

    public function testSetLogLevel()
    {
        $this->debugger->set_log_level(1);
        $this->debugger->info('This message should not be logged');
        $this->assertLogNotContains('[Info]');

        $this->debugger->error('This message should be logged');
        $this->assertLogContains('[Error]');
    }

    public function testSetConsole()
    {
        $this->expectOutputString('');
        $this->debugger->set_console(true);
        $this->expectOutputString('[' . date('Y-m-d H:i:s') . '] This is a console message');
        $this->debugger->logger('This is a console message');
    }

    private function assertLogContains(string $expected): void
    {
        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString($expected, $content);
    }

    private function assertLogNotContains(string $expected): void
    {
        if (file_exists($this->logFile)) {
            $content = file_get_contents($this->logFile);
            $this->assertStringNotContainsString($expected, $content);
        }
    }
}
?>

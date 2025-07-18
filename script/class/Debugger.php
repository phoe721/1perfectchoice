<?php

/**
 * Class Debugger
 *
 * A simple logging class to log messages at different levels (info, notice, warning, error).
 * The log messages can be output to a file and optionally to the console.
 */
class Debugger
{
    /**
     * @var int $loglevel The current logging level.
     */
    private int $loglevel = 0;

    /**
     * @var bool $console Whether to output log messages to the console.
     */
    private bool $console = false;

    /**
     * @var string $log_file The file path to write log messages.
     */
    private string $log_file = "/home/hshgp28zfpja/public_html/1perfectchoice/log/1perfectchoice.log";

    /**
     * Logs an info level message.
     *
     * @param string $message The message to log.
     */
    public function info(string $message): void
    {
        if ($this->loglevel >= 3) {
            $this->logger("[Info][" . basename($_SERVER['SCRIPT_NAME']) . "] $message");
        }
    }

    /**
     * Logs a notice level message.
     *
     * @param string $message The message to log.
     */
    public function notice(string $message): void
    {
        if ($this->loglevel >= 2) {
            $this->logger("[Notice][" . basename($_SERVER['SCRIPT_NAME']) . "] $message");
        }
    }

    /**
     * Logs an error level message.
     *
     * @param string $message The message to log.
     */
    public function error(string $message): void
    {
        if ($this->loglevel >= 1) {
            $this->logger("[Error][" . basename($_SERVER['SCRIPT_NAME']) . "] $message");
        }
    }

    /**
     * Logs a warning level message.
     *
     * @param string $message The message to log.
     */
    public function warning(string $message): void
    {
        if ($this->loglevel >= 0) {
            $this->logger("[Warning][" . basename($_SERVER['SCRIPT_NAME']) . "] $message");
        }
    }

    /**
     * Sets the logging level.
     *
     * @param int $level The logging level (0 = warning, 1 = error, 2 = notice, 3 = info).
     */
    public function set_log_level(int $level): void
    {
        $this->loglevel = $level;
    }

    /**
     * Sets whether to output log messages to the console.
     *
     * @param bool $state True to output to the console, false otherwise.
     */
    public function set_console(bool $state): void
    {
        $this->console = $state;
    }

    /**
     * Logs a message to the log file and optionally to the console.
     *
     * @param string $msg The message to log.
     */
    public function logger(string $msg): void
    {
        $timestring = date('Y-m-d H:i:s');
        $msg = "[$timestring] $msg";
        if ($this->console) {
            echo $msg;
        }
        $file = fopen($this->log_file, 'a+');
        if ($file) {
            fwrite($file, $msg . PHP_EOL);
            fclose($file);
        }
    }

    /**
     * Send a message to the console only.
     *
     * @param string $msg The message to console.
     */
    public function console(string $msg): void
    {
        echo $msg;
    }
}
?>

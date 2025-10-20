<?php
class Stopwatch {
    private float $startTime = 0;
    private float $endTime = 0;
    private bool $running = false;

    // 開始計時
    public function start(): void {
        if (!$this->running) {
            $this->startTime = microtime(true);
            $this->running = true;
            $this->endTime = 0;
        }
    }

    // 停止計時
    public function stop(): void {
        if ($this->running) {
            $this->endTime = microtime(true);
            $this->running = false;
        }
    }

    // 重置計時器
    public function reset(): void {
        $this->startTime = 0;
        $this->endTime = 0;
        $this->running = false;
    }

    // 取得經過時間（秒），如果還在計時，則計算當下時間
    public function getElapsedTime(): float {
        if ($this->running) {
            return microtime(true) - $this->startTime;
        } elseif ($this->startTime > 0 && $this->endTime > 0) {
            return $this->endTime - $this->startTime;
        }
        return 0.0;
    }
}
?>
<?php

namespace Mmoreram\GearmanBundle\Module;


class JobStatus
{
    private bool $known;
    private bool $running;
    private int $completed;
    private int $completionTotal;

    public function __construct(array $response)
    {
        $this->known = $response[0] ?? false;
        $this->running = $response[1] ?? false;
        $this->completed = (int)($response[2] ?? 0);
        $this->completionTotal = (int)($response[3] ?? 0);
    }

    public function isKnown(): bool
    {
        return $this->known;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function getCompleted(): int
    {
        return $this->completed;
    }

    public function getCompletionTotal(): int
    {
        return $this->completionTotal;
    }

    /**
     * Return percent completed.
     *
     * 0 is not started or not known
     * 1 is finished
     * Between 0 and 1 is in process. Value is a float
     *
     */
    public function getCompletionPercent(): float
    {
        $percent = 0;

        if (($this->completed > 0) && ($this->completionTotal > 0)) {
            $percent = $this->completed / $this->completionTotal;
        }

        return $percent;
    }

    public function isFinished(): bool
    {
        return $this->isKnown() && !$this->isRunning() && $this->getCompletionPercent() == 1;
    }
}

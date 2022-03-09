<?php

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Module\WorkerClass as Worker;

class WorkerCollection
{

    private array $workerClasses = [];

    public function add(Worker $workerClass): self
    {
        $this->workerClasses[] = $workerClass;

        return $this;
    }

    public function toArray(): array
    {
        $workersDumped = [];

        foreach ($this->workerClasses as $worker) {
            $workersDumped[] = $worker->toArray();
        }

        return $workersDumped;
    }
}

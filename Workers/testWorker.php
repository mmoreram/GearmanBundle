<?php

namespace Mmoreramerino\GearmanBundle\Workers;

/**
 * @Gearman\Work(description="Worker test description")
 */
class testWorker {
    
    /**
     * @Gearman\Job(iter=3, name="test", description="This is a description")
     */
    public function testA($job)
    {
        echo 'Job testA done!'.PHP_EOL;
    }
    /**
     * @Gearman\Job
     */
    public function testB($job)
    {
        echo 'Job testB done!'.PHP_EOL;
    }
}
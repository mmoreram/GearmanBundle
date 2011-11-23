<?php

namespace Mmoreramerino\GearmanBundle\Workers;

/** @Gearman\Work(description="Worker test description") */
class testWorker
{

    /**
     * Test method to run as a job
     * 
     * @param \GearmanJob $job Object with job parameters
     *
     * @Gearman\Job(iter=3, name="test", description="This is a description")     *
     */
    public function testA(\GearmanJob $job)
    {
        echo get_class($job);die();
        echo 'Job testA done!'.PHP_EOL;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     * 
     * @Gearman\Job
     */
    public function testB(\GearmanJob $job)
    {
        echo 'Job testB done!'.PHP_EOL;
    }
}
<?php

namespace Mmoreramerino\GearmanBundle\Workers;

/** @Gearman\Work(description="Worker test description", defaultMethod="doBackground") */
class testWorker
{

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job(iterations=3, name="test", description="This is a description", defaultMethod="doHighBackground")     *
     */
    public function testA(\GearmanJob $job)
    {
        echo 'Job testA done!'.PHP_EOL;

        return true;
    }

    /**
     * Test method to run as a job
     *
     * @param \GearmanJob $job Object with job parameters
     *
     * @return boolean
     *
     * @Gearman\Job
     */
    public function testB(\GearmanJob $job)
    {
        echo 'Job testB done!'.PHP_EOL;

        return true;
    }
}

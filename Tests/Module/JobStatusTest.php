<?php

/**
 * Gearman Bundle for Symfony2
 * 
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Module;

use Mmoreram\GearmanBundle\Module\JobStatus;

/**
 * Tests JobStatusTest class
 */
class JobStatusTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Testing when job does not exist
     */
    public function testJobStatusNonExistant()
    {
        $jobStatus = new JobStatus(array(
            false,
            false,
            null,
            null
        ));

        $this->assertFalse($jobStatus->isKnown());
        $this->assertFalse($jobStatus->isRunning());
        $this->assertEquals($jobStatus->getCompleted(), 0);
        $this->assertEquals($jobStatus->getCompletionTotal(), 0);
        $this->assertEquals($jobStatus->getCompletionPercent(), 0);
        $this->assertFalse($jobStatus->isFinished());
    }


    /**
     * Testing when job is started
     */
    public function testJobStatusStarted()
    {
        $jobStatus = new JobStatus(array(
            true,
            true,
            0,
            10
        ));

        $this->assertTrue($jobStatus->isKnown());
        $this->assertTrue($jobStatus->isRunning());
        $this->assertEquals($jobStatus->getCompleted(), 0);
        $this->assertEquals($jobStatus->getCompletionTotal(), 10);
        $this->assertEquals($jobStatus->getCompletionPercent(), 0);
        $this->assertFalse($jobStatus->isFinished());
    }


    /**
     * Testing when job is still running
     */
    public function testJobStatusRunning()
    {
        $jobStatus = new JobStatus(array(
            true,
            true,
            5,
            10
        ));

        $this->assertTrue($jobStatus->isKnown());
        $this->assertTrue($jobStatus->isRunning());
        $this->assertEquals($jobStatus->getCompleted(), 5);
        $this->assertEquals($jobStatus->getCompletionTotal(), 10);
        $this->assertEquals($jobStatus->getCompletionPercent(), 0.5);
        $this->assertFalse($jobStatus->isFinished());
    }


    /**
     * Testing when job is already finished
     */
    public function testJobStatusFinished()
    {
        $jobStatus = new JobStatus(array(
            true,
            false,
            10,
            10
        ));

        $this->assertTrue($jobStatus->isKnown());
        $this->assertFalse($jobStatus->isRunning());
        $this->assertEquals($jobStatus->getCompleted(), 10);
        $this->assertEquals($jobStatus->getCompletionTotal(), 10);
        $this->assertEquals($jobStatus->getCompletionPercent(), 1);
        $this->assertTrue($jobStatus->isFinished());
    }
}
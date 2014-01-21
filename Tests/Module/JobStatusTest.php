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
     * Testing job status
     *
     * @dataProvider dataProvider
     */
    public function testJobStatusNonExistant($known, $running, $completed, $completionTotal, $isKnown, $isRunning, $getCompleted, $getCompletionTotal, $getCompletionPercent, $isFinished)
    {
        $jobStatus = new JobStatus(array(
            $known,
            $running,
            $completed,
            $completionTotal
        ));

        $this->assertEquals($jobStatus->isKnown(), $isKnown);
        $this->assertEquals($jobStatus->isRunning(), $isRunning);
        $this->assertEquals($jobStatus->getCompleted(), $getCompleted);
        $this->assertEquals($jobStatus->getCompletionTotal(), $getCompletionTotal);
        $this->assertEquals($jobStatus->getCompletionPercent(), $getCompletionPercent);
        $this->assertEquals($jobStatus->isFinished(), $isFinished);
    }


    /**
     * Data provider
     */
    public function dataProvider()
    {
        return array(

            /**
             * Testing when job does not exist
             */
            array(
                false,
                false,
                null,
                null,
                false,
                false,
                0,
                0,
                0,
                false
            ),

            /**
             * Testing when job is started
             */
            array(
                true,
                true,
                0,
                10,
                true,
                true,
                0,
                10,
                0,
                false
            ),

            /**
             * Testing when job is still running
             */
            array(
                true,
                true,
                5,
                10,
                true,
                true,
                5,
                10,
                0.5,
                false
            ),

            /**
             * Testing when job is already finished
             */
            array(
                true,
                false,
                10,
                10,
                true,
                false,
                10,
                10,
                1,
                true
            ),


        );
    }
}
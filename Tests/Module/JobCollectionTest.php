<?php

/**
 * RSQueueBundle for Symfony2
 *
 * Marc Morera 2013
 */

namespace Mmoreram\GearmanBundle\Tests\Module;

use Mmoreram\GearmanBundle\Module\JobCollection;
use Mmoreram\GearmanBundle\Module\JobClass as Job;

/**
 * Tests JobClassTest class
 */
class JobCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var JobCollection
     * 
     * Job Collection
     */
    private $jobCollection;



    /**
     * Setup
     */
    public function setUp()
    {
        $this->jobCollection = new JobCollection;
    }


    public function 
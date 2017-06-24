<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Mmoreram\GearmanBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * GearmanWorkStartingEvent
 *
 * @author David Moreau <dmoreau@lafourchette.com>
 */
class GearmanWorkStartingEvent extends Event
{
    /**
     * @var array
     *
     * Gearman jobs running
     */
    protected $jobs;

    /**
     * Construct method
     *
     * @param array $jobs Jobs
     */
    public function __construct(array $jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * Get Gearman Work subscribed jobs
     *
     * @return array Subscribed jobs
     */
    public function getJobs()
    {
        return $this->jobs;
    }
}

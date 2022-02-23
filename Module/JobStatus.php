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

namespace Mmoreram\GearmanBundle\Module;

/**
 * Job status class
 *
 * @since 2.3.1
 */
class JobStatus
{
    /**
     * @var boolean
     *
     * Job is known
     */
    private $known;

    /**
     * @var boolean
     *
     * Job is running
     */
    private $running;

    /**
     * @var Integer
     *
     * Job completition
     */
    private $completed;

    /**
     * @var integer
     *
     * Job completition total
     */
    private $completionTotal;

    /**
     * Construct method
     *
     * @param array $response Response to parse
     */
    public function __construct(array $response)
    {
        $this->known = (isset($response[0]) && $response[0]);
        $this->running = (isset($response[1]) && $response[1]);

        $this->completed = (isset($response[2]) && !$response[2])
            ? 0
            : $response[2];

        $this->completionTotal = (isset($response[3]) && !$response[3])
            ? 0
            : $response[3];
    }

    /**
     * Return if job is known
     *
     * @return boolean Job is still known
     */
    public function isKnown()
    {
        return $this->known;
    }

    /**
     * Return if job is still running
     *
     * @return boolean Jon is still running
     */
    public function isRunning()
    {
        return $this->running;
    }

    /**
     * Return completed value
     *
     * @return integer Completed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Return completition total
     *
     * @return integer Completition total
     */
    public function getCompletionTotal()
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
     * @return float Percent completed
     */
    public function getCompletionPercent()
    {
        $percent = 0;

        if (($this->completed > 0) && ($this->completionTotal > 0)) {
            $percent = $this->completed / $this->completionTotal;
        }

        return $percent;
    }

    /**
     * Return if job is still running
     *
     * @return boolean Jon is still running
     */
    public function isFinished()
    {
        return $this->isKnown() && !$this->isRunning() && $this->getCompletionPercent() == 1;
    }
}

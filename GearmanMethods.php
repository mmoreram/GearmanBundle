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

namespace Mmoreram\GearmanBundle;

/**
 * Gearman available methods
 *
 * @since 2.3.1
 */
class GearmanMethods
{
    /**
     * Gearman method do
     *
     * The GearmanClient::do() method is deprecated as of pecl/gearman 1.0.0.
     * Use GearmanClient::doNormal().
     *
     * @see http://www.php.net/manual/en/gearmanclient.do.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DO = 'do';

    /**
     * Gearman method doNormal
     *
     * @see http://www.php.net/manual/en/gearmanclient.donormal.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DONORMAL = 'doNormal';

    /**
     * Gearman method doLow
     *
     * @see http://www.php.net/manual/en/gearmanclient.dolow.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DOLOW = 'doLow';

    /**
     * Gearman method doHigh
     *
     * @see http://www.php.net/manual/en/gearmanclient.dohigh.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DOHIGH = 'doHigh';

    /**
     * Gearman method doBackground
     *
     * @see http://php.net/manual/en/gearmanclient.dobackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DOBACKGROUND = 'doBackground';

    /**
     * Gearman method doLowBackgound
     *
     * @see http://php.net/manual/en/gearmanclient.dolowbackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DOLOWBACKGROUND = 'doLowBackground';

    /**
     * Gearman method doHighBackground
     *
     * @see http://php.net/manual/en/gearmanclient.dohighbackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_DOHIGHBACKGROUND = 'doHighBackground';

    /**
     * Tasks methods
     */

    /**
     * Gearman method addTask
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtask.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASK = 'addTask';

    /**
     * Gearman method addTaskLow
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtasklow.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASKLOW = 'addTaskLow';

    /**
     * Gearman method addTaskHigh
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtaskhigh.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASKHIGH = 'addTaskHigh';

    /**
     * Gearman method addTaskBackground
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtaskbackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASKBACKGROUND = 'addTaskBackground';

    /**
     * Gearman method addTaskLowBackground
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtasklowbackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASKLOWBACKGROUND = 'addTaskLowBackground';

    /**
     * Gearman method addTaskHighBackground
     *
     * @see http://www.php.net/manual/en/gearmanclient.addtaskhighbackground.php
     *
     * @var string
     */
    public const GEARMAN_METHOD_ADDTASKHIGHBACKGROUND = 'addTaskHighBackground';
}

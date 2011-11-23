<?php

namespace Mmoreramerino\GearmanBundle\Module;

use Mmoreramerino\GearmanBundle\Driver\Gearman\Job;
use Mmoreramerino\GearmanBundle\Driver\Gearman\Work;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueMissingException;
use Mmoreramerino\GearmanBundle\Exceptions\SettingValueBadFormatException;

/**
 * Job class
 *
 * @author Marc Morera <marc@ulabox.com>
 */
class JobClass
{
    /**
     * Callable name for this job
     * If is setted on annotations, this value will be used
     *  otherwise, natural method name will be used.
     *
     * @var string
     */
    private $callableName;


    /**
     * Description of Job
     *
     * @var string
     */
    private $description;

    /**
     * Construct method
     *
     * @param Job               $methodAnnotation  MethodAnnotation class
     * @param \ReflectionMethod $method            ReflextionMethod class
     * @param Work              $classAnnotation   Work class
     * @param string            $callableNameClass Callable name class
     * @param array             $settings          Settings structure
     */
    public function __construct( Job $methodAnnotation, \ReflectionMethod $method, Work $classAnnotation, $callableNameClass, array $settings)
    {
        $this->callableName =   (null !== $methodAnnotation->name) ?
                                    $methodAnnotation->name :
                                    $method->getName();

        $this->methodName   =   $method->getName();

        $this->realCallableName = str_replace('\\', '', $callableNameClass.'~'.$this->callableName);
        $this->description  =    (null !== $method->getDocComment()) ?
                                    $methodAnnotation->description :
                                    'No description is defined';

        if (null !== $settings['defaults']['iter']) {
            $iter = (int) ($settings['defaults']['iter']);

            if (null !== $classAnnotation->iter) {
                $iter = (int) ($classAnnotation->iter);
            }

            if (null !== $methodAnnotation->iter) {
                $iter = (int) ($methodAnnotation->iter);
            }
        } else {
            throw new SettingValueMissingException('defaults/iter');
        }
        $this->iter = $iter;

        /**
         * Servers definition for job
         */
        $servers = array();
        if (null !== $settings['defaults']['servers']) {
            if (is_array($settings['defaults']['servers'])) {

                foreach ($settings['defaults']['servers'] as $name => $server) {
                    $servername = $server['hostname'].':'.(int) ($server['port']);
                    $servers[$name] = $servername;
                }
            } else {

                throw new SettingValueBadFormatException('servers');
            }

            if (null !== $classAnnotation->servers) {
                if (is_array($classAnnotation->servers)) {
                    $servers = $classAnnotation->servers;
                } else {
                    $servers = array($classAnnotation->servers);
                }
            }

            if (null !== $methodAnnotation->servers) {
                if (is_array($methodAnnotation->servers)) {
                    $servers = $methodAnnotation->servers;
                } else {
                    $servers = array($methodAnnotation->servers);
                }
            }
        } else {
            throw new SettingValueMissingException('defaults/servers');
        }
        $this->servers = $servers;
    }

    /**
     * Retrieve all Job data in cache format
     *
     * @return array
     */
    public function __toCache()
    {
        return array(
            'callableName'          =>  $this->callableName,
            'methodName'            =>  $this->methodName,
            'realCallableName'      =>  $this->realCallableName,
            'description'           =>  $this->description,
            'iter'                  =>  $this->iter,
            'servers'               =>  $this->servers,
        );
    }
}

<?php

namespace Mmoreram\GearmanBundle\Module;

use Mmoreram\GearmanBundle\Driver\Gearman\Job;
use Mmoreram\GearmanBundle\Driver\Gearman\Work;
use Symfony\Component\DependencyInjection\ContainerAware;
use Mmoreram\GearmanBundle\Exceptions\SettingValueMissingException;
use Mmoreram\GearmanBundle\Exceptions\SettingValueBadFormatException;

/**
 * Job class
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */
class JobClass extends ContainerAware
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
        $this->description  =    (null !== $methodAnnotation->description) ?
                                    $methodAnnotation->description :
                                    'No description is defined';

        if (!isset($settings['defaults'])) {
            throw new SettingValueMissingException('defaults');
        }

        if (isset($settings['defaults']['iterations']) && null !== $settings['defaults']['iterations']) {
            $iter = (int) ($settings['defaults']['iterations']);

            if (null !== $classAnnotation->iterations) {
                $iter = (int) ($classAnnotation->iterations);
            }

            if (null !== $methodAnnotation->iterations) {
                $iter = (int) ($methodAnnotation->iterations);
            }
        } else {
            throw new SettingValueMissingException('defaults/iterations');
        }
        $this->iterations = $iter;


        if (isset($settings['defaults']['method']) && null !== $settings['defaults']['method']) {
            $defaultMethod = ($settings['defaults']['method']);

            if (null !== $classAnnotation->defaultMethod) {
                $defaultMethod = ($classAnnotation->defaultMethod);
            }

            if (null !== $methodAnnotation->defaultMethod) {
                $defaultMethod = ($methodAnnotation->defaultMethod);
            }
        } else {
            throw new SettingValueMissingException('defaults/method');
        }

        $this->defaultMethod = $defaultMethod;

        /**
         * Servers definition for job
         */
        $servers = array();
        if (isset($settings['defaults']['servers']) && null !== $settings['defaults']['servers']) {
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
            'iterations'			=>  $this->iterations,
            'servers'               =>  $this->servers,
            'defaultMethod'         =>  $this->defaultMethod,
        );
    }
}

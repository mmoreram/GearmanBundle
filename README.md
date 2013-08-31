#GearmanBundle for Symfony2

##Installation
You have to add require line into you composer.json file

    "require": {
        "php": ">=5.3.3",
        "symfony/symfony": "2.3.*",
        ...
        "mmoreram/gearman-bundle": "dev-master"
    },

Then you have to use composer to update your project dependencies

    php composer.phar update

And register the bundle in your appkernel.php file

    return array(
        // ...
        new Liip\DoctrineCacheBundle\LiipDoctrineCacheBundle(),
        new Mmoreram\GearmanBundle\GearmanBundle(),
        // ...
    );

## Configuration
We must configure our Worker. Common definitions must be defined in config.yml file, setting values for all installed Workers. 
Also we must config gearman cache, using doctrine cache.
    
    liip_doctrine_cache:
        namespaces:
            gearman:
                type: file_system


    gearman:
        # Bundles will parsed searching workers
        bundles:
            # Name of bundle
            AcmeBundle:

                # Bundle namespace
                namespace: Mmoreramerino\TestBundle

                # Bundle search can be enabled or disabled
                active: true

                # If any include is defined, Only these namespaces will be parsed
                # Otherwise, full Bundle will be parsed
                include:
                    - Services
                    - EventListener

                # Namespaces this Bundle will ignore when parsing
                ignore:
                    - DependencyInjection
                    - Resources

        # default values
        # All these values will be used if are not overwritten in Workers or jobs
        defaults:

            # default method related with all jobs
            # do
            # doBackground
            # doHigh
            # doHighBackground
            # doLow
            # doLowBackground
            method: do

            # Default number of executions before job dies.
            # If annotations defined, will be overwritten
            # If empty, 150 is defined by default
            iterations: 150

        # Server list where workers and clients will connect to
        # Each server must contain host and port
        # If annotations defined, will be full overwritten
        #
        # If servers empty, simple localhost server is defined by default
        # If port empty, 4730 is defined by efault
        servers:
            localhost:
                host: 127.0.0.1
                port: 4730

In development mode you do not want to cache things over more than one request. An easy solution for this is to use the array cache in the dev environment ( Extracted from [LiipDoctrineCacheBundle](https://github.com/liip/LiipDoctrineCacheBundle#development-mode) documentation )

    #config.yml
    liip_doctrine_cache:
        namespaces:
            presta_sitemap:
                type: file_system

    # config_dev.yml
    liip_doctrine_cache:
        namespaces:
            presta_sitemap:
                type: array

## Workers and Jobs definition

This Bundle allows you to configure whatever as a Job. It provides you an easy way to execute it with Supervisor, for example. Moreover, it let you call client methods in Symfony2 environment in a really simple and practical way.  
Job annotations always overwrite work annotations, and work annotations always overwrite environment settings.

    <?php

    namespace Acme\AcmeBundle\Workers; 

    use Mmoreram\GearmanBundle\Driver\Gearman; 

    /**
     * @Gearman\Work(
     *     iterations = 3, 
     *     description = "Worker test description",
     *     defaultMethod = "do",
     *     servers = {
     *         { "host": "192.168.1.1", "port": 4560 },
     *         { "host": "192.168.1.2", "port": 4560 }, 
     *     }
     * )
     */
    class AcmeWorker
    {
        /**
        * Test method to run as a job
        *
        * @param \GearmanJob $job Object with job parameters
        *
        * @return boolean
        *
        * @Gearman\Job(
        *     iterations = 3, 
        *     name = "test", 
        *     description = "This is a description"
        * )
        */
        public function testA(\GearmanJob $job)
        {
            echo 'Job testA done!' . PHP_EOL;

            return true;
        }

        /**
        * Test method to run as a job
        *
        * @param \GearmanJob $job Object with job parameters
        *
        * @return boolean
        *
        * @Gearman\Job(
        *     defaultMethod = "doLowBackground"
        * )
        */
        public function testB(\GearmanJob $job)
        {
            echo 'Job testB done!' . PHP_EOL;

            return true;
        }
    }

### Worker Annotations

    /**
     * @Gearman\Work(
     *     name = "MyAcmeWorker",
     *     iterations = 3, 
     *     description = "Acme Worker. Containing multiple available jobs",
     *     defaultMethod = "do",
     *     servers = {
     *         { "host": "192.168.1.1", "port": 4560 },
     *         { "host": "192.168.1.2", "port": 4560 }, 
     *     }
     * )
     */

* name : Name of work. You can associate a group of jobs with some keyword
* description : Short description about all jobs inside
* iterations : You can overwrite iterations of all jobs inside
* servers : array containing servers providers will connect to offer all jobs
* service : You can use even a service. Must specify callable service name
* defaultMethod : You can define witch method will be used as default in all jobs

### Job Annotations

    /**
     * @Gearman\Job(
     *     name = "doSomething",
     *     iterations = 10, 
     *     description = "Acme Job action. This is just a description of a method that do something",
     *     defaultMethod = "doBackground",
     *     servers = { "host": "192.168.1.1", "port": 4560 }
     * )
     */

* name : Name of job. You will use it to call job
* description : Short description about this job. Important field
* iterations : You can overwrite iterations of this job.
* servers : array containing servers providers will connect to offer this job
* defaultMethod : You can define witch method will be used as default in this job

### Service as a Worker

If you want to use your service as a worker, you have to specify service variable in Worker annotation

    <?php

    namespace Acme\AcmeBundle\Services; 

    /**
     * @Gearman\Work(
     *     service="myServiceName"
     * )
     */
    class AcmeService
    {
 
        ... some code ...

        /**
        * Test method to run as a job
        *
        * @param \GearmanJob $job Object with job parameters
        *
        * @return boolean
        *
        * @Gearman\Job()
        */
        public function testA(\GearmanJob $job)
        {
            echo 'Job testA done!' . PHP_EOL;

            return true;
        }
    }

And have this service defined in your dependency injection definition file

    # /Resources/config/services.yml
    bundles:
        Services:
            myServiceName:
                class: Acme\AcmeBundle\Services\AcmeService
                arguments: 
                    event_dispatcher: @event_dispatcher
                    mailer: @mailer
  
## Workers and Jobs execution

Gearman provides a set of commands that will make easier to know all workers settings.

    /app/console

    gearman
        gearman:cache:clear                   Clears gearman cache data on current environment
        gearman:cache:warmup                  Warms up gearman cache data
        gearman:job:describe                  Describe given job
        gearman:job:execute                   Execute one single job
        gearman:worker:describe               Describe given worker
        gearman:worker:execute                Execute one worker with all contained Jobs
        gearman:worker:list                   List all Gearman Workers and their Jobs

### Workers and Jobs list

Once all your workers are defined, you can simply list them to ensure all settings are correct.

    /app/console gearman:workers:list

    @Worker:  Mmoreramerino\TestBundle\Services\AcmeWorker
    callablename:  MmoreramerinoTestBundleServicesMyAcmeWorker
    Jobs:
      - #1
          name: testA
          callablename: MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething


### Worker settings

You can describe full worker using its callableName.  
This command provides you all information about desired Worker, overwritting custom annotation settings to default config settings.  
This command also provides you all needed information to work with Supervisord.

    php app/console gearman:worker:describe MmoreramerinoTestBundleServicesMyAcmeWorker
    
    @Worker\className : Mmoreramerino\TestBundle\Services\AcmeWorker
    @Worker\fileName : /var/www/projects/myrepo/src/Mmoreramerino/TestBundle/Services/AcmeWorker.php
    @Worker\nameSpace : Mmoreramerino\TestBundle\Services
    @Worker\callableName: MmoreramerinoTestBundleServicesMyAcmeWorker
    @Worker\supervisord : /usr/bin/php /var/www/projects/myrepo/app/console gearman:worker:execute MmoreramerinoTestBundleServicesMyAcmeWorker --no-interaction
    @worker\iterations : 3
    @Worker\#jobs : 1

    @worker\servers :

        #0 - 192.168.1.1:4560
        #1 - 192.168.1.2:4560

    @Worker\description :

        Acme Worker. Containing multiple available jobs

### Job settings

You can also describe full job using also its callableName
This command provides you all information about desired Job, overwritting custom annotation settings to worker settings.  
This command also provides you all needed information to work with Supervisord.

    php app/console gearman:job:describe MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething

    @Worker\className : Mmoreramerino\TestBundle\Services\AcmeWorker
    @Worker\fileName : /var/www/projects/myrepo/src/Mmoreramerino/TestBundle/Services/AcmeWorker.php
    @Worker\nameSpace : Mmoreramerino\TestBundle\Services
    @Worker\callableName: MmoreramerinoTestBundleServicesMyAcmeWorker
    @Worker\supervisord : /usr/bin/php /var/www/projects/myrepo/app/console gearman:worker:execute MmoreramerinoTestBundleServicesMyAcmeWorker --no-interaction
    @worker\iterations : 3
    @Worker\#jobs : 1

    @worker\servers :

        #0 - 192.168.1.1:4560
        #1 - 192.168.1.2:4560

    @Worker\description :

        Acme Worker. Containing multiple available jobs

    @job\methodName : testA
    @job\callableName : MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething
    @job\supervisord : /usr/bin/php /var/www/projects/myrepo/app/console gearman:job:execute MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething --no-interaction
    @job\iterations : 10
    @job\defaultMethod : doBackground
    @job\servers :

        0 - 192.168.1.1:4560

    @job\description :

        #Acme Job action. This is just a description of a method that do something

### Job and Worker execution

You can execute by command line an instance of a worker or a job.  
The difference between them is that an instance of a worker can execute any of their jobs, without assignning any priority to them, and a job only can run itself.

    php app/console gearman:worker:execute MmoreramerinoTestBundleServicesMyAcmeWorker
    php app/console gearman:job:execute MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething


## Gearman Service

You can request a Job by using the gearman service.

    $this
        ->getContainer()
        ->get('gearman');

### Jobs

    $result = $gearman
        ->doJob('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', json_encode(array('value1')));

* doJob : Call the job and wait for the result
* doNormalJob : Call the job and wait for the result ( Only newest gearman versions )
* doHighJob : Call the job and wait for the result on High Preference
* doLowJob : Call the job and wait for the result on Low Preference
* doBackroundJob : Call the job without waiting for the result. 
    * It recieves a job handle for the submitted job
* doHighBackgroundJob : Call the job without waitting for the result on High Preference. 
    * It recieves a job handle for the submitted job
* doLowBackgroundJob : Call the job without waitting for the result on Low Preference.
    * It recieves a job handle for the submitted job
* callJob : Call the job with default method.
    * Defined in settings, work annotations or the job annotations

### Tasks

    $gearman
        ->addTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value1')
        ->addLowTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value2')
        ->addHighBackgroundTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value3')
        ->runTasks();

* addTask : Adds a task to be run in parallel with other tasks
* addTaskHigh : Add a high priority task to run in parallel
* addTaskLow : Add a low priority task to run in parallel
* addTaskBackground : Add a background task to be run in parallel
* addTaskHighBackground : Add a high priority background task to be run in parallel
* addTaskLowBackground : Add a low priority background task to be run in parallel
* runTasks : Run a list of tasks in parallel


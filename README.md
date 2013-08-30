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

##Configuration
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

## Defining your workers
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
     *     servers = (
     *         ( "host": "192.168.1.1", "port": 4560 ),
     *         ( "host": "192.168.1.2", "port": 4560 ), 
     *     )
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
        public function testA(\GearmanJob $job)
        {
            echo 'Job testB done!' . PHP_EOL;

            return true;
        }
    }

### Worker Annotations

* name : Name of work. You can associate a group of jobs with some keyword
* description : Short description about all jobs inside
* iterations : You can overwrite iterations of all jobs inside
* servers : array containing servers providers will connect to offer all jobs
* service : You can use even a service. Must specify callable service name
* defaultMethod : You can define witch method will be used as default in all jobs

### Job Annotations

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
            echo 'Job testB done!' . PHP_EOL;

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

### Executing your workers

Once all your workers are defined, you can simply 
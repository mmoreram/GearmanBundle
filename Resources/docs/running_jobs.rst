Running your jobs
=================

Gearman provides a set of commands that will make easier to know all workers
settings.

.. code-block:: bash

    $ php app/console

A subset of listed commands are Gearman specific.

.. code-block:: bash

    gearman
        gearman:cache:clear     Clears gearman cache data on current environment
        gearman:cache:warmup    Warms up gearman cache data
        gearman:job:describe    Describe given job
        gearman:job:execute     Execute one single job
        gearman:worker:describe Describe given worker
        gearman:worker:execute  Execute one worker with all contained Jobs
        gearman:worker:list     List all Gearman Workers and their Jobs

Listing workers and jobs
~~~~~~~~~~~~~~~~~~~~~~~~

Once all your workers are defined, you can simply list them to ensure all
settings are correct.

.. code-block:: bash

    $ php app/console gearman:worker:list

    @Worker:  Mmoreramerino\TestBundle\Services\AcmeWorker
    callablename:  MmoreramerinoTestBundleServicesMyAcmeWorker
    Jobs:
      - #1
          name: testA
          callablename: MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething

Listing worker settings
~~~~~~~~~~~~~~~~~~~~~~~

You can describe full worker using its callableName.
This command provides you all information about desired Worker, overwritting
custom annotation settings to default config settings.
This command also provides you all needed information to work with Supervisord.

.. code-block:: bash

    $ php app/console gearman:worker:describe MmoreramerinoTestBundleServicesMyAcmeWorker

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

Listing job settings
~~~~~~~~~~~~~~~~~~~~

You can also describe full job using also its callableName
This command provides you all information about desired Job, overwritting custom
annotation settings to worker settings.
This command also provides you all needed information to work with Supervisord.

.. code-block:: bash

    $ php app/console gearman:job:describe MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething

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

Run a job
~~~~~~~~~

You can execute by command line an instance of a worker or a job.
The difference between them is that an instance of a worker can execute any of
their jobs, without assignning any priority to them, and a job only can run
itself.

.. code-block:: bash

    $ php app/console gearman:worker:execute MmoreramerinoTestBundleServicesMyAcmeWorker
    $ php app/console gearman:job:execute MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething

.. note:: By using callableName you can let Supervisord maintain alive a worker.
          When the job is executed as times as iterations is defined, will die,
          but supervisord will alive it again.
          You can have as many as worker instances as you want.
          Get some `Supervisord`_ info

Overriding default settings
~~~~~~~~~~~~~~~~~~~~~~~~~~~

From the command line you can run the jobs or workers with overridden settings.  These include

- iterations
- minimum-execution-time
- timeout

For example:

.. code-block:: bash

    $ php app/console gearman:job:describe MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething --iterations=5 --minimum-execution-time=2 --timeout=20

If these options are ommited, then the configuration defaults are used.

Request job status
~~~~~~~~~~~~~~~~~~

With the Handle given if requesting a background job you can request the status
of the job. The Method returns a JobStatus object placed in
`Mmoreram\GearmanBundle\Module\JobStatus'

.. code-block:: php

    $jobStatus = $gearman->getJobStatus($result);
    $jobIsKnown = $jobStatus->isKnown();
    $jobIsRunning = $jobStatus->isRunning();
    $jobIsFinished = $jobStatus->isFinished();

    /**
     * Also gives completion data
     */
    $completed = $jobStatus->getCompleted();
    $completionTotal = $jobStatus->getCompletionTotal();
    $completionPercent = $jobStatus->getCompletionPercent();

.. _Supervisord: http://supervisord.org/

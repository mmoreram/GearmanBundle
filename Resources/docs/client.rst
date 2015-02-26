Client
======

You can request a Job by using the gearman client.

.. code-block:: php

    $this
        ->getContainer()
        ->get('gearman');

Servers
~~~~~~~

.. code-block:: php

    $gearman
        ->clearServers()
        ->setServer('127.1.1.1', 4677)
        ->addServer('127.1.1.1', 4678)
        ->addServer('127.1.1.1', 4679);


- addServer: Add new server to requested client
- setServer: Clean server list and set new server to requested client
- clearServers: Clear server list

.. note:: By default, if no server is set, gearman will use server defined as
          default in config.yml
   host: *127.0.0.1*
   port: *4730*

Request a job
~~~~~~~~~~~~~

.. code-block:: php

    $result = $gearman
        ->doJob('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', json_encode(array('value1')));

    $returnCode = $gearman->getReturnCode();

- doJob: Call the job and wait for the result
- doNormalJob: Call the job and wait for the result ( Only newest gearman versions )
- doHighJob: Call the job and wait for the result on High Preference
- doLowJob: Call the job and wait for the result on Low Preference
- doBackroundJob: Call the job without waiting for the result.
    - It receives a job handle for the submitted job
- doHighBackgroundJob: Call the job without waitting for the result on High Preference.
    - It receives a job handle for the submitted job
- doLowBackgroundJob: Call the job without waitting for the result on Low Preference.
    - It receives a job handle for the submitted job
- callJob: Call the job with default method.
    - Defined in settings, work annotations or the job annotations
- getReturnCode: Retrieve the return code from the last requested job.

Tasks
~~~~~

.. code-block:: php

    $gearman
        ->addTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value1', $context1)
        ->addLowTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value2', $context2)
        ->addHighBackgroundTask('MmoreramerinoTestBundleServicesMyAcmeWorker~doSomething', 'value3', $context3)
        ->runTasks();

- addTask: Adds a task to be run in parallel with other tasks
- addTaskHigh: Add a high priority task to run in parallel
- addTaskLow: Add a low priority task to run in parallel
- addTaskBackground: Add a background task to be run in parallel
- addTaskHighBackground: Add a high priority background task to be run in parallel
- addTaskLowBackground: Add a low priority background task to be run in parallel
- runTasks: Run a list of tasks in parallel
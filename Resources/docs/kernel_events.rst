Kernel Events
=============

GearmanBundle transforms Gearman callbacks to Symfony2 kernel events.

Complete Callback
~~~~~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackCompleteEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setCompleteCallback](http://www.php.net/manual/en/gearmanclient.setcompletecallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.complete, method: onComplete }

Created Callback
~~~~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackCreatedEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setCreatedCallback](http://www.php.net/manual/en/gearmanclient.setcreatedcallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.created, method: onCreated }

Data Callback
~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackDataEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setDataCallback](http://www.php.net/manual/en/gearmanclient.setdatacallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.data, method: onData }

Exception Callback
~~~~~~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackExceptionEvent` with no methods.
For more information about this GearmanEvent, read [GearmanClient::setExceptionCallback](http://www.php.net/manual/en/gearmanclient.setexceptioncallback.php) documentation.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.exception, method: onExcept }

Fail Callback
~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackFailEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setFailCallback](http://www.php.net/manual/en/gearmanclient.setfailcallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.fail, method: onFail }

Status Callback
~~~~~~~~~~~~~~~

This event receives as a parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackStatusEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setStatusCallback](http://www.php.net/manual/en/gearmanclient.setstatuscallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.status, method: onStatus }

Warning Callback
~~~~~~~~~~~~~~~~

This event receives as parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackWarningEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setWarningCallback](http://www.php.net/manual/en/gearmanclient.setwarningcallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.warning, method: onWarning }

Workload Callback
~~~~~~~~~~~~~~~~~

This event receives as parameter an instance of `Mmoreram\GearmanBundle\Event\GearmanClientCallbackWorkloadEvent` with methods `$event->getGearmanTask()` and `&$event->getContext()`.
First method returns an instance of `\GearmanTask`.
For more information about this GearmanEvent, read [GearmanClient::setWorkloadCallback](http://www.php.net/manual/en/gearmanclient.setworkloadcallback.php) documentation.
The second method will return `$context` that you could add in the `addTask()` method.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.client.callback.workload, method: onWorkload }

Starting Work Event
~~~~~~~~~~~~~~~~~~

This event receives as parameter an instanceof `Mmoreram\GearmanBundle\Event\GearmanWorkStartingEvent` with one method:
`$event->getJobs()` returns the configuration of the jobs.

This event is dispatched before a job starts.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.work.starting, method: onWorkStarting }

Execute Work Event
~~~~~~~~~~~~~~~~~~

This event receives as parameter an instanceof `Mmoreram\GearmanBundle\Event\GearmanWorkExecutedEvent` with three methods:
`$event->getJobs()` returns the configuration of the jobs,
`$event->getIterationsRemaining()` returns the remaining iterations for these jobs,
`$event->getReturnCode()` returns the return code of the last executed job.

This event is dispatched after a job has been completed.  After this event is completed, the worker continues with its iterations.

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
              - { name: kernel.event_listener, event: gearman.work.executed, method: onWorkExecuted }

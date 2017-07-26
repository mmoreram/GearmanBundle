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
~~~~~~~~~~~~~~~~~~~

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

GearmanJob Events
-----------------

As only synchronous calls might be monitored by Gearman callbacks, set of kernel events was added for observation of job
on Gearman side. Each event is invoked with `Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanJobEvent` descendant
class instance. This allows you to access `\GearmanJob` instance and all its parameters. It is possible but highly not
recommended to use any methods of `\GearmanJob` instance resulting in sending data to client.

Those events are dispatched for both synchronous and asynchronous jobs invocation. Be aware that invocation of callbacks
on client side won't be affected by those worker side events.

Job Complete Event
~~~~~~~~~~~~~~~~~~

This event is dispatched each time job sends completion information to client. It means calling `\GearmanJob::sendComplete()`
method from job method. Each event listener will receive `Mmoreram\GearmanBundle\Event\Worker\JobCompleteEvent` object.
Listener method should follow below declaration:

.. code-block:: php

    public function onComplete(Mmoreram\GearmanBundle\Event\Worker\JobCompleteEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.complete, method: onComplete }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.complete" method="onComplete" />
    </service>

Job Data Event
~~~~~~~~~~~~~~

This event is dispatched each time job sends data to gearman server and client. It means calling `\GearmanJob::sendData()`
method from job method. Each event listener will receive `Mmoreram\GearmanBundle\Event\Worker\JobDataEvent` object.
Listener method should follow below declaration:

.. code-block:: php

    public function onData(Mmoreram\GearmanBundle\Event\Worker\JobDataEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.data, method: onData }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.data" method="onData" />
    </service>

Job Exception Event
~~~~~~~~~~~~~~~~~~~

This event is dispatched each time job communicates to Gearman Job Server exception occurrence. It  means calling
`\GearmanJob::sendException()` method from job method. Each event listener receives `Mmoreram\GearmanBundle\Event\Worker\JobExceptionEvent` object.
Listener method shoud follow below declaration:

.. code-block:: php

    public function onException(Mmoreram\GearmanBundle\Event\Worker\JobExceptionEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.exception, method: onException }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.exception" method="onException" />
    </service>

Job Fail Event
~~~~~~~~~~~~~~

This event is dispatched when job comunicates failure to Gearman Job Server by calling `\GearmanJob::sendFail()` method.
Event listener receives `Mmoreram\GearmanBundle\Event\Worker\JobExceptionEvent` object. As sendFail method is called without
arguments, above event object provide only access to `\GearmanJob` instance.
Listener method should follow below declaration:

.. code-block:: php

    public function onFail(Mmoreram\GearmanBundle\Event\Worker\JobFailEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.fail, method: onFail }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.fail" method="onFail" />
    </service>

Job Return Event
~~~~~~~~~~~~~~~~

This event is dispatched when job sends return value to Gearman Job Server by calling `\GearmanJob::sendReturn` method.
Event listener receives `Mmoreram\GearmanBundle\Event\Worker\JobReturnEvent` object.
Listener method should follow below declaration:

.. code-block:: php

    public function onReturn(Mmoreram\GearmanBundle\Event\Worker\JobReturnEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.return, method: onReturn }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.return" method="onReturn" />
    </service>

Job Status Event
~~~~~~~~~~~~~~~~

This event is dispatched each time job sends status update to Gearman Job Server by calling `\GearmanJob::sendReturn`
method. Event listener receives `Mmoreram\GearmanBundle\Event\Worker\JobStatusEvent` object.
Listener method should follow below declaration:

.. code-block:: php

    public function onStatus(Mmoreram\GearmanBundle\Event\Worker\JobStatusEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.status, method: onStatus }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.status" method="onStatus" />
    </service>

Job Warning Event
~~~~~~~~~~~~~~~~~

This event is dispatched when job sends warning message to Gearman Job Server by calling `\GearmanJob::sendWarning`
method. Event listener receives `Mmoreram\GearmanBundle\Event\Worker\JobWarningEvent` object.
Listener method should follow below declaration:

.. code-block:: php

    public function onWarning(Mmoreram\GearmanBundle\Event\Worker\JobWarningEvent $event);

To hook listener to above event using Yaml configuration:

.. code-block:: yml

    services:
        my_event_listener:
            class: AcmeBundle\EventListener\MyEventListener
            tags:
                - { name: kernel.event_listener, event: gearman.worker.job.warning, method: onWarning }

Same declaration using xml configuration would look like this:

.. code-block:: xml

    <service id="my_event_listener" class="AcmeBundle\EventListener\MyEventListener">
        <tag name="kernel.event_listener" event="gearman.worker.job.warning" method="onWarning" />
    </service>

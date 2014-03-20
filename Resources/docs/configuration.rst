Configuration
=============

We must configure our Worker. Common definitions must be defined in
config.yml file, setting values for all installed Workers. Also we must
config gearman cache, using doctrine cache.

.. note:: If ``iterations`` value is 0, worker will not kill itself never, so
          thread will be alive as long as needed. The reason to allow workers
          to kill themselves is just to prevent each process to accumulate a
          large quantity of memory.

.. code-block:: yml

    liip_doctrine_cache:
       namespaces:
          gearman:
             type: file_system

    gearman:
       # Bundles will parsed searching workers
       bundles:
          # Name of bundle
          AcmeBundle:

             # Bundle name
             name: MmoreramerinoTestBundle

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

          # Default method related with all jobs
          # do // deprecated as of pecl/gearman 1.0.0. Use doNormal
          # doNormal
          # doBackground
          # doHigh
          # doHighBackground
          # doLow
          # doLowBackground
          method: doNormal

          # Default number of executions before job dies.
          # If annotations defined, will be overwritten
          # If empty, 0 is defined by default
          iterations: 150

          # execute callbacks after operations using Kernel events
          callbacks: true

          # Prefix in all jobs
          # If empty name will not be modified
          # Useful for rename jobs in different environments
          job_prefix: null

          # Autogenerate unique key in jobs/tasks if not set
          # This key is unique given a Job name and a payload serialized
          generate_unique_key: true

          # Prepend namespace when callableName is built
          # By default this variable is set as true
          workers_name_prepend_namespace: true

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

In development mode you do not want to cache things over more than one
request. An easy solution for this is to use the array cache in the dev
environment ( Extracted from `LiipDoctrineCacheBundle`_ documentation )

.. code-block:: yml

    #config.yml
    liip_doctrine_cache:
        namespaces:
            gearman:
                type: file_system

In development mode you do not want to cache things over more than one
request. An easy solution for this is to use the array cache in the dev
environment ( Extracted from `LiipDoctrineCacheBundle`_ documentation )

.. code-block:: yml

    #config_dev.yml
    liip_doctrine_cache:
        namespaces:
            gearman:
                type: array

.. _LiipDoctrineCacheBundle: https://github.com/liip/LiipDoctrineCacheBundle#development-mode
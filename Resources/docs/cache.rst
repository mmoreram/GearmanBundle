Cache
=====

GearmanBundle caches all annotations. You can clear or warmup just gearman cache
by using custom commands

.. code-block:: bash

    $ php app/console

    gearman
        gearman:cache:clear     Clears gearman cache data on current environment
        gearman:cache:warmup    Warms up gearman cache data

Gearman also clear and warmup cache when using Symfony2 cache commands

.. code-block:: bash

    $ php app/console

    cache
        cache:clear             Clears the cache
        cache:warmup            Warms up an empty cache

Installing/Configuring
======================

Tags
~~~~

-  Use last unstable version (alias of ``dev-master``) to stay always
   in sync with the last commit
-  Use last stable version tag to stay in a stable release.
-  |LatestUnstableVersion| |LatestStableVersion|

.. note:: Since Symfony2 versions 2.1 and 2.2 are not maintained anymore,
          and since these branches were in sync with master branch, they
          both have been deleted

Installing `Gearman`_
~~~~~~~~~~~~~~~~~~~~~

To install Gearman Job Server with ``apt-get`` use the following
commands:

.. code-block:: bash

    $ sudo apt-get install gearman-job-server

And start server

.. code-block:: bash

    $ sudo service gearman-job-server start

Then you need to install the **Gearman driver** using the following command - 
you will find all available gearman versions in `Pear Repository`_

.. code-block:: bash

    $ pecl install channel://pecl.php.net/gearman-X.X.X

Finally, you need to register the php module

.. code-block:: bash

    $ echo "extension=gearman.so" > /etc/php5/conf.d/gearman.ini

Installing `GearmanBundle`_
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Install composer if not already done:

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php

You have to add this bundle as a project requirement:

.. code-block:: bash

    composer require mmoreram/gearman-bundle

Finally, register GearmanBundle in your app/AppKernel.php file:

.. code-block:: php

    $bundles = [
       // ...
       new Mmoreram\GearmanBundle\GearmanBundle(),
       // ...
    ];

.. _Gearman: http://gearman.org
.. _Pear Repository: http://pecl.php.net/package/gearman
.. _GearmanBundle: https://github.com/mmoreram/GearmanBundle

.. |LatestUnstableVersion| image:: https://poser.pugx.org/mmoreram/gearman-bundle/v/unstable.png
   :target: https://packagist.org/packages/mmoreram/gearman-bundle
.. |LatestStableVersion| image:: https://poser.pugx.org/mmoreram/gearman-bundle/v/stable.png
   :target: https://packagist.org/packages/mmoreram/gearman-bundle

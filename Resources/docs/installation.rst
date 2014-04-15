Installing/Configuring
======================

Tags
~~~~

-  Use last unstable version ( alias of ``dev-master`` ) to stay always
   in last commit
-  Use last stable version tag to stay in a stable release.
-  |LatestUnstableVersion| |LatestStableVersion|

.. note:: As long as Symfony2 versions 2.1 and 2.2 are not maintained anymore,
          and as long as these branches had same code than master branch, they
          all have been deleted

Installing `Gearman`_
~~~~~~~~~~~~~~~~~~~~~

To install Gearman Job Server with ``apt-get`` use the following
commands:

.. code-block:: bash

    $ sudo apt-get install gearman-job-server

And start server

.. code-block:: bash

    $ service gearman-job-server start

Then you need to install **Gearman driver** using the following commands

.. code-block:: bash

    $ pecl install channel://pecl.php.net/gearman-X.X.X

You will find all available gearman versions in `Pear Repository`_
Finally you need to start php module

.. code-block:: bash

    $ echo "extension=gearman.so" > /etc/php5/conf.d/gearman.ini

Installing `GearmanBundle`_
~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have to add require line into you composer.json file

.. code-block:: yml

    "require": {
       "php": ">=5.3.3",
       "symfony/symfony": "2.3.*",

       "mmoreram/gearman-bundle": "dev-master"
    }

Then you have to use composer to update your project dependencies

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar update

And register the bundle in your appkernel.php file

.. code-block:: php

    return array(
       // ...
       new Doctrine\DoctrineCacheBundle\DoctrineCacheBundle(),
       new Mmoreram\GearmanBundle\GearmanBundle(),
       // ...
    );

.. _Gearman: http://gearman.org
.. _Pear Repository: http://pecl.php.net/package/gearman
.. _GearmanBundle: https://github.com/mmoreram/GearmanBundle

.. |LatestUnstableVersion| image:: https://poser.pugx.org/mmoreram/gearman-bundle/v/unstable.png
   :target: https://packagist.org/packages/mmoreram/gearman-bundle
.. |LatestStableVersion| image:: https://poser.pugx.org/mmoreram/gearman-bundle/v/stable.png
   :target: https://packagist.org/packages/mmoreram/gearman-bundle

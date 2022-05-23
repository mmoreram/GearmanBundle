Customize
=========

Some bundle behaviours can be overwritten

Custom unique job identifier method
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want a custom method to generate custom unique values for your jobs when
not defined ( specified in generate_unique_key ), you only have to extend
default UniqueJobIdentifierGenerator class and overwrite generateUniqueKey
method, as folowing example.

.. code-block:: php

    <?php

    namespace My\Custom\Namespace;

    use Mmoreram\GearmanBundle\Generator\UniqueJobIdentifierGenerator;

    class MyCustomUniqueJobIdentifierGenerator extends UniqueJobIdentifierGenerator
    {

        public function generateUniqueKey(string $name, string $params, ?string $unique, ?string $method = null): ?string
        {
            /**
             * Custom generation
             */
        }
    }

You need also to overwrite in your config.yml the generator class

.. code-block:: yml

    parameters:

        #
        # Generators
        #
        gearman.unique_job_identifier.class: My\Custom\Namespace\MyCustomUniqueJobIdentifierGenerator

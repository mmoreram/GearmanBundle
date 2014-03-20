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

    /**
     * Gearman Bundle for Symfony2
     *
     * @author Marc Morera <yuhu@mmoreram.com>
     * @since 2013
     */

    namespace My\Custom\Namespace;

    use Mmoreram\GearmanBundle\Generator\UniqueJobIdentifierGenerator;

    /**
     * Gearman execute methods. All Worker methods
     *
     * @author Marc Morera <yuhu@mmoreram.com>
     */
    class MyCustomUniqueJobIdentifierGenerator extends UniqueJobIdentifierGenerator
    {

        /**
         * Generate unique key if generateUniqueKey is enabled
         *
         * $this->generateUniqueKey can be used as is protected in parent class
         *
         * @param string $name   A GermanBundle registered function to be executed
         * @param string $params Parameters to send to task as string
         * @param string $unique unique ID used to identify a particular task
         * @param string $method Method to perform
         *
         * @return string Generated Unique Key
         */
        public function generateUniqueKey($name, $params, $unique, $method)
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
